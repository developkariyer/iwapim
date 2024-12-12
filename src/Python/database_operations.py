import pandas as pd
from sqlalchemy import create_engine
from db_config_loader import get_mysql_config

def fetch_data(asin, sales_channel, yaml_path):
    engine = None  # Initialize engine
    try:
        # Load MySQL configuration
        mysql_config = get_mysql_config(yaml_path)

        # Create SQLAlchemy engine
        db_url = f"mysql+mysqlconnector://{mysql_config['user']}:{mysql_config['password']}@{mysql_config['host']}:{mysql_config['port']}/{mysql_config['database']}"
        engine = create_engine(db_url)

        query = """
        SELECT sale_date AS ds, total_quantity AS y
        FROM iwa_amazon_daily_sales_summary
        WHERE asin = %s AND sales_channel = %s
          AND sale_date >= DATE_SUB(CURDATE(), INTERVAL 2 YEAR)
          AND sale_date < CURDATE()
          AND data_source = 1
        ORDER BY sale_date ASC;
        """
        # Execute the query and load data into a pandas DataFrame
        df = pd.read_sql(query, engine, params=(asin, sales_channel))
        return df

    except Exception as e:
        print(f"Error fetching data for ASIN {asin} and Sales Channel {sales_channel}: {e}")
        return pd.DataFrame()

    finally:
        if engine:
            engine.dispose()



def fetch_pairs(yaml_path):
    engine = None  # Initialize engine
    try:
        mysql_config = get_mysql_config(yaml_path)

        db_url = f"mysql+mysqlconnector://{mysql_config['user']}:{mysql_config['password']}@{mysql_config['host']}:{mysql_config['port']}/{mysql_config['database']}"
        engine = create_engine(db_url)

        query = """
        SELECT DISTINCT asin, sales_channel
        FROM iwa_amazon_daily_sales_summary
        WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 2 YEAR)
          AND sale_date < CURDATE()
        """

        df = pd.read_sql(query, engine)
        return df

    except Exception as e:
        print(f"Error fetching ASIN/Sales Channel pairs: {e}")
        return pd.DataFrame()

    finally:
        if engine:
            engine.dispose()


from sqlalchemy import create_engine, text
from db_config_loader import get_mysql_config

def insert_forecast_data(forecast_data, asin, sales_channel, yaml_path):
    """
    Inserts or updates forecasted data into the MySQL `iwa_amazon_daily_sales_summary` table.

    Args:
        forecast_data (pd.DataFrame): Forecasted data with columns:
                                      - 'ds': Date
                                      - 'yhat': Predicted sales
        asin (str): The ASIN of the product.
        sales_channel (str): The sales channel (e.g., 'Amazon.com').
        yaml_path (str): Path to the YAML configuration file for MySQL connection.

    Returns:
        None
    """
    engine = None  # Initialize engine
    connection = None
    try:
        # Load MySQL configuration
        mysql_config = get_mysql_config(yaml_path)

        # Create SQLAlchemy engine
        db_url = f"mysql+mysqlconnector://{mysql_config['user']}:{mysql_config['password']}@{mysql_config['host']}:{mysql_config['port']}/{mysql_config['database']}"
        engine = create_engine(db_url)
        connection = engine.connect()

        # Prepare data for insertion
        forecast_data['asin'] = asin
        forecast_data['sales_channel'] = sales_channel
        forecast_data['data_source'] = 0  # 0 indicates forecasted data
        forecast_data = forecast_data.rename(columns={'ds': 'sale_date', 'yhat': 'total_quantity'})

        # Convert DataFrame to a list of tuples
        rows_to_insert = list(forecast_data[['asin', 'sales_channel', 'sale_date', 'total_quantity', 'data_source']].itertuples(index=False, name=None))

        # SQL query for INSERT or UPDATE
        insert_query = text("""
        INSERT INTO iwa_amazon_daily_sales_summary (asin, sales_channel, sale_date, total_quantity, data_source)
        VALUES (:asin, :sales_channel, :sale_date, :total_quantity, :data_source)
        ON DUPLICATE KEY UPDATE
            total_quantity = VALUES(total_quantity),
            data_source = VALUES(data_source)
        """)

        # Execute batch insert/update
        #with connection.begin() as transaction:
        #    connection.execute(insert_query, [{'asin': row[0], 'sales_channel': row[1], 'sale_date': row[2], 'total_quantity': row[3], 'data_source': row[4]} for row in rows_to_insert])

    except Exception as e:
        print(f"Error inserting/updating forecast data for ASIN {asin} and Sales Channel {sales_channel}: {e}")

    finally:
        if connection:
            connection.close()
        if engine:
            engine.dispose()
