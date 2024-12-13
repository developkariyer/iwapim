import pandas as pd
from sqlalchemy import create_engine, text
from db_config_loader import get_mysql_config
import math
import pymysql

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


def fetch_pairs(yaml_path, scenario):
    """
    Fetch distinct ASIN and sales_channel pairs based on the specified scenario.

    Parameters:
        yaml_path (str): Path to the YAML configuration file.
        scenario (int): Processing scenario (1 to 5).
            1: Process Amazon.eu only
            2: Process sales_channel = 'all' only
            3: Process Amazon.com only
            4: Process all other than Amazon.eu, Amazon.com, and 'all'
            5: Process all channels without filter

    Returns:
        pd.DataFrame: DataFrame containing ASIN and sales_channel pairs.
    """
    engine = None  # Initialize engine
    try:
        # Load MySQL configuration
        mysql_config = get_mysql_config(yaml_path)

        # Build database connection URL
        db_url = (
            f"mysql+mysqlconnector://{mysql_config['user']}:{mysql_config['password']}@"
            f"{mysql_config['host']}:{mysql_config['port']}/{mysql_config['database']}"
        )
        engine = create_engine(db_url)

        # Define base query
        base_query = """
        SELECT DISTINCT asin, sales_channel
        FROM iwa_amazon_daily_sales_summary
        WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 2 YEAR)
          AND sale_date < CURDATE()
        """

        # Add filters based on the scenario
        if scenario == 1:
            query = base_query + "AND sales_channel = 'Amazon.eu'"
        elif scenario == 2:
            query = base_query + "AND sales_channel = 'all'"
        elif scenario == 3:
            query = base_query + "AND sales_channel = 'Amazon.com'"
        elif scenario == 4:
            query = base_query + "AND sales_channel NOT IN ('Amazon.eu', 'Amazon.com', 'all')"
        elif scenario == 5:
            query = base_query  # No additional filters
        else:
            raise ValueError("Invalid scenario. Must be between 1 and 5.")

        # Execute query and return results
        df = pd.read_sql(query, engine)
        return df

    except Exception as e:
        print(f"Error fetching ASIN/Sales Channel pairs: {e}")
        return pd.DataFrame()

    finally:
        if engine:
            engine.dispose()


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
    # Ensure the forecast_data contains the required columns
    required_columns = {'ds', 'yhat'}
    if not required_columns.issubset(forecast_data.columns):
        raise ValueError(f"Forecast data must contain columns: {required_columns}")

    connection = None
    try:
        # Load MySQL configuration
        mysql_config = get_mysql_config(yaml_path)

        # Connect to the database
        connection = pymysql.connect(
            host=mysql_config['host'],
            user=mysql_config['user'],
            password=mysql_config['password'],
            database=mysql_config['database'],
            port=mysql_config.get('port', 3306),
            cursorclass=pymysql.cursors.DictCursor,
        )

        # SQL query for iwasku mapping
        iwasku_query = """
        SELECT COALESCE(
            (SELECT regvalue FROM iwa_registry WHERE regtype = 'asin-to-iwasku' AND regkey = %s), %s
        ) AS iwasku
        """

        # SQL query for inserting or updating forecast data
        insert_query = """
        INSERT INTO iwa_amazon_daily_sales_summary (asin, sales_channel, iwasku, sale_date, total_quantity, data_source)
        VALUES (%s, %s, %s, %s, %s, %s)
        ON DUPLICATE KEY UPDATE
            total_quantity = VALUES(total_quantity),
            data_source = VALUES(data_source)
        """

        with connection.cursor() as cursor:
            # Fetch iwasku mapping
            cursor.execute(iwasku_query, (asin, asin))
            iwasku_result = cursor.fetchone()
            iwasku = iwasku_result['iwasku'] if iwasku_result else asin

            # Prepare data for insertion
            forecast_data['asin'] = asin
            forecast_data['sales_channel'] = sales_channel
            forecast_data['iwasku'] = iwasku
            forecast_data['data_source'] = 0  # 0 indicates forecasted data
            forecast_data = forecast_data.rename(columns={'ds': 'sale_date', 'yhat': 'total_quantity'})
            forecast_data['total_quantity'] = forecast_data['total_quantity'].apply(lambda x: int(math.ceil(max(x, 0))))
            forecast_data['sale_date'] = forecast_data['sale_date'].dt.strftime('%Y-%m-%d')  # Ensure DATE format

            # Insert rows
            rows_to_insert = [
                (
                    row.asin,
                    row.sales_channel,
                    row.iwasku,
                    row.sale_date,
                    row.total_quantity,
                    row.data_source
                )
                for row in forecast_data.itertuples(index=False)
            ]

            for row in rows_to_insert:
                cursor.execute(insert_query, row)

            connection.commit()

    except Exception as e:
        print(f"Error inserting/updating forecast data: {e}")
        raise

    finally:
        if connection:
            connection.close()


def delete_forecast_data(asin, sales_channel, yaml_path):
    """
    Deletes old forecast data for the given ASIN and sales_channel from the database.

    Args:
        asin (str): The ASIN for which to delete forecast data.
        sales_channel (str): The sales_channel for which to delete forecast data.
        yaml_path (str): Path to the YAML configuration file for database connection.

    Returns:
        None
    """
    conn = None
    try:
        # Load MySQL configuration from YAML file
        mysql_config = get_mysql_config(yaml_path)

        # Connect to the database
        connection = pymysql.connect(
            host=mysql_config['host'],
            user=mysql_config['user'],
            password=mysql_config['password'],
            database=mysql_config['database'],
            port=mysql_config.get('port', 3306),
            cursorclass=pymysql.cursors.DictCursor,
        )
        cursor = connection.cursor()

        # Define the delete query
        query = """
        DELETE FROM iwa_amazon_daily_sales_summary
        WHERE asin = %s
          AND sales_channel = %s
          AND data_source = 0
        """

        # Execute the delete query
        cursor.execute(query, (asin, sales_channel))
        connection.commit()

    except pymysql.MySQLError as e:
        print(f"Error deleting forecast data for ASIN {asin}, Sales Channel {sales_channel}: {e}")

    finally:
        if connection:
            connection.close()
