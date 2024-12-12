from database_operations import fetch_pairs, fetch_data, insert_forecast_data
from forecast_generator import generate_forecast

def run_forecast_pipeline(yaml_path):
    """
    Executes the complete forecasting pipeline for all ASIN/Sales Channel pairs.

    Args:
        yaml_path (str): Path to the YAML configuration file for MySQL connection.

    Returns:
        None
    """
    # Step 1: Fetch ASIN/Sales Channel pairs
    print("Fetching ASIN/Sales Channel pairs...")
    pairs = fetch_pairs(yaml_path)

    if pairs.empty:
        print("No ASIN/Sales Channel pairs found. Exiting...")
        return

    # Step 2: Iterate through each ASIN/Sales Channel pair
    for _, row in pairs.iterrows():
        asin = row['asin']
        sales_channel = row['sales_channel']
        print(f"Processing ASIN: {asin}, Sales Channel: {sales_channel}")

        try:
            # Step 3: Fetch historical sales data
            data = fetch_data(asin, sales_channel, yaml_path)

            if data.empty:
                print(f"No data found for ASIN {asin} and Sales Channel {sales_channel}. Skipping...")
                continue

            # Step 4: Generate forecast
            forecast = generate_forecast(data, forecast_days=180)

            # Step 5: Insert forecast into the database
            insert_forecast_data(forecast, asin, sales_channel, yaml_path)
            print(f"Forecasting completed for ASIN {asin} and Sales Channel {sales_channel}")

        except Exception as e:
            print(f"Error processing ASIN {asin} and Sales Channel {sales_channel}: {e}")

    print("Forecasting pipeline completed.")

# Entry point for the script
if __name__ == "__main__":
    yaml_path = '/var/www/iwapim/config/local/database.yaml'
    run_forecast_pipeline(yaml_path)
