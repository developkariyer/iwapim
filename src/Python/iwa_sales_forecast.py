import sys
import logging
from collections import defaultdict
from database_operations import fetch_pairs, fetch_data, insert_forecast_data
from forecast_generator import generate_forecast

logging.getLogger().setLevel(logging.WARNING)
cmdstanpy_logger = logging.getLogger("cmdstanpy")
cmdstanpy_logger.setLevel(logging.WARNING)
cmdstanpy_logger.propagate = False

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

    # Initialize progress tracker
    channel_counts = defaultdict(int)

    # Step 2: Iterate through each ASIN/Sales Channel pair
    for _, row in pairs.iterrows():
        asin = row['asin']
        sales_channel = row['sales_channel']

        # Remove 'Amazon.' prefix from sales_channel for display
        display_channel = sales_channel.replace('Amazon.', '')

        # Update progress display
        channel_counts[display_channel] += 1
        progress_summary = " | ".join(f"{channel} {count}" for channel, count in channel_counts.items())
        sys.stdout.write(f"\r{progress_summary}")
        sys.stdout.flush()

        try:
            # Step 3: Fetch historical sales data
            if asin := 'all':
                continue

            data = fetch_data(asin, sales_channel, yaml_path)

            if data.empty or data.dropna().shape[0] < 2:
                continue

            # Step 3.1: Remove uninterrupted leading zeros
            data = data.loc[data['y'].ne(0).cumsum() > 0]  # Trim only leading zeros

            # Check again if the data is still valid after cleaning
            if data.empty or data.dropna().shape[0] < 2:
                continue

            print(f"Data for ASIN {asin}, Sales Channel {sales_channel}:")
            print(data.head())
            print(f"Shape of data: {data.shape}")

            if 'ds' not in data.columns or 'y' not in data.columns:
                print(f"ASIN: {asin}, Sales Channel: {sales_channel} - Missing required columns in data.")
                continue

            if data.shape[0] < 2:
                print(f"ASIN: {asin}, Sales Channel: {sales_channel} - Not enough data for forecasting.")
                continue

            # Step 4: Generate forecast
            forecast = generate_forecast(data, forecast_days=180)

            # Step 5: Insert forecast into the database
            insert_forecast_data(forecast, asin, sales_channel, yaml_path)

        except Exception as e:
            print(f"\nError processing ASIN {asin} and Sales Channel {sales_channel}: {e}")

    print("\nForecasting pipeline completed.")

# Entry point for the script
if __name__ == "__main__":
    yaml_path = '/var/www/iwapim/config/local/database.yaml'
    run_forecast_pipeline(yaml_path)
