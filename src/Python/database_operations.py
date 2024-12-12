import pandas as pd
from mysql.connector import connect, Error
from db_config_loader import get_mysql_config

def fetch_data(asin, sales_channel, yaml_path):
    """
    Fetches 2 years of daily sales data for a specific asin and sales_channel from MySQL.

    Args:
        asin (str): The ASIN of the product.
        sales_channel (str): The sales channel (e.g., 'Amazon.com').
        yaml_path (str): Path to the YAML configuration file for MySQL connection.

    Returns:
        pd.DataFrame: A DataFrame with columns 'ds' (date) and 'y' (sales quantity).
    """
    try:
        # Load MySQL configuration
        mysql_config = get_mysql_config(yaml_path)

        # Connect to the database
        connection = connect(**mysql_config)
        query = """
        SELECT sale_date AS ds, total_quantity AS y
        FROM iwa_amazon_daily_sales_summary
        WHERE asin = %s AND sales_channel = %s
          AND sale_date >= DATE_SUB(CURDATE(), INTERVAL 2 YEAR)
          AND sale_date < CURDATE()
        ORDER BY sale_date ASC;
        """
        # Execute the query and load data into a pandas DataFrame
        df = pd.read_sql(query, connection, params=(asin, sales_channel))
        return df

    except Error as e:
        print(f"Error fetching data for ASIN {asin} and sales channel {sales_channel}: {e}")
        return pd.DataFrame()  # Return empty DataFrame in case of error

    finally:
        if connection and connection.is_connected():
            connection.close()

def fetch_pairs(yaml_path):
    """
    Fetches the list of unique ASIN and sales_channel pairs from the database.

    Args:
        yaml_path (str): Path to the YAML configuration file for MySQL connection.

    Returns:
        pd.DataFrame: A DataFrame containing unique pairs of 'asin' and 'sales_channel'.
    """
    try:
        # Load MySQL configuration
        mysql_config = get_mysql_config(yaml_path)

        # Connect to the database
        connection = connect(**mysql_config)
        query = """
        SELECT DISTINCT asin, sales_channel
        FROM iwa_amazon_daily_sales_summary
        WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 2 YEAR) AND sale_date < CURDATE()
        LIMIT 1
        """
        # Execute the query and load data into a pandas DataFrame
        df = pd.read_sql(query, connection)
        return df

    except Error as e:
        print(f"Error fetching ASIN/Sales Channel pairs: {e}")
        return pd.DataFrame()  # Return empty DataFrame in case of error

    finally:
        if connection and connection.is_connected():
            connection.close()

def insert_forecast_data(forecast_data, asin, sales_channel, yaml_path):
    """
    Inserts forecasted data into the MySQL `iwa_amazon_daily_sales_summary` table.

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
    try:
        # Load MySQL configuration
        from db_config_loader import get_mysql_config
        mysql_config = get_mysql_config(yaml_path)

        # Connect to the database
        connection = mysql.connector.connect(**mysql_config)
        cursor = connection.cursor()

        # Insert forecast data
        insert_query = """
        INSERT INTO iwa_amazon_daily_sales_summary (asin, sales_channel, sale_date, total_quantity, data_source)
        VALUES (%s, %s, %s, %s, %s)
        """
        rows_to_insert = [
            (asin, sales_channel, row['ds'], row['yhat'], 0)
            for _, row in forecast_data.iterrows()
        ]

        # Execute batch insert
        cursor.executemany(insert_query, rows_to_insert)
        connection.commit()

        print(f"Inserted {cursor.rowcount} rows for ASIN {asin} and Sales Channel {sales_channel}.")

    except Error as e:
        print(f"Error inserting forecast data for ASIN {asin} and Sales Channel {sales_channel}: {e}")

    finally:
        if connection and connection.is_connected():
            cursor.close()
            connection.close()
