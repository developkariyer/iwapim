import pandas as pd
from sqlalchemy import create_engine
from db_config_loader import get_mysql_config

def fetch_data(asin, sales_channel, yaml_path):
    engine = None
    try:
        mysql_config = get_mysql_config(yaml_path)

        db_url = f"mysql+mysqlconnector://{mysql_config['user']}:{mysql_config['password']}@{mysql_config['host']}:{mysql_config['port']}/{mysql_config['database']}"
        engine = create_engine(db_url)

        query = """
        SELECT sale_date AS ds, total_quantity AS y
        FROM iwa_amazon_daily_sales_summary
        WHERE asin = :asin AND sales_channel = :sales_channel
          AND sale_date >= DATE_SUB(CURDATE(), INTERVAL 2 YEAR)
          AND sale_date < CURDATE()
        ORDER BY sale_date ASC;
        """
        params = {'asin': asin, 'sales_channel': sales_channel}

        df = pd.read_sql(query, engine, params=params)
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


def insert_forecast_data(forecast_data, asin, sales_channel, yaml_path):
    engine = None  # Initialize engine
    connection = None
    try:
        mysql_config = get_mysql_config(yaml_path)

        db_url = f"mysql+mysqlconnector://{mysql_config['user']}:{mysql_config['password']}@{mysql_config['host']}:{mysql_config['port']}/{mysql_config['database']}"
        engine = create_engine(db_url)
        connection = engine.connect()

        forecast_data['asin'] = asin
        forecast_data['sales_channel'] = sales_channel
        forecast_data['data_source'] = 1  # 1 indicates forecasted data
        forecast_data = forecast_data.rename(columns={'ds': 'sale_date', 'yhat': 'total_quantity'})

        forecast_data[['asin', 'sales_channel', 'sale_date', 'total_quantity', 'data_source']].to_sql(
            'iwa_amazon_daily_sales_summary',
            con=connection,
            if_exists='append',
            index=False,
            method='multi'
        )

        print(f"Inserted forecast data for ASIN {asin} and Sales Channel {sales_channel}.")

    except Exception as e:
        print(f"Error inserting forecast data for ASIN {asin} and Sales Channel {sales_channel}: {e}")

    finally:
        if connection:
            connection.close()
        if engine:
            engine.dispose()
