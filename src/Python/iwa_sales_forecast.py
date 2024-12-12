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

        # Update progress display
        channel_counts[sales_channel] += 1
        progress_summary = " | ".join(f"{channel} {count}" for channel, count in channel_counts.items())
        sys.stdout.write(f"\r{progress_summary}")
        sys.stdout.flush()

        try:
            # Step 3: Fetch historical sales data
            data = fetch_data(asin, sales_channel, yaml_path)

            if data.empty:
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
