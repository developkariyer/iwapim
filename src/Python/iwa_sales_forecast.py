import sys
import os
import logging
from collections import defaultdict
from database_operations import fetch_pairs, fetch_data, insert_forecast_data, delete_forecast_data
from forecast_generator import generate_forecast, generate_forecast_arima, generate_forecast_neuralprophet

logging.getLogger().setLevel(logging.WARNING)
cmdstanpy_logger = logging.getLogger("cmdstanpy")
cmdstanpy_logger.setLevel(logging.WARNING)
cmdstanpy_logger.propagate = False

def run_forecast_pipeline(yaml_path, scenario):
    """
    Executes the complete forecasting pipeline for all ASIN/Sales Channel pairs.

    Args:
        yaml_path (str): Path to the YAML configuration file for MySQL connection.
        scenario (int): Processing scenario (1 to 5).
            1: Process Amazon.eu only
            2: Process sales_channel = 'all' only
            3: Process Amazon.com only
            4: Process all other than Amazon.eu, Amazon.com, and 'all'
            5: Process all channels without filter.

    Returns:
        None
    """
    # Step 1: Fetch ASIN/Sales Channel pairs based on the scenario
    print("Fetching ASIN/Sales Channel pairs...")
    pairs = fetch_pairs(yaml_path, scenario)

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
        progress_summary = "|".join(f"{channel} {count}" for channel, count in channel_counts.items())
        sys.stdout.write(f"\r{progress_summary}")
        sys.stdout.flush()

        try:
            delete_forecast_data(asin, sales_channel, yaml_path)

            # Step 3: Fetch historical sales data
            data = fetch_data(asin, sales_channel, yaml_path)

            # Validate initial data
            if data.empty or data.dropna().shape[0] < 2:
                continue

            if data['y'].sum() == 0:
                continue

            # Remove uninterrupted leading zeros
            first_non_zero_idx = data['y'].ne(0).idxmax()  # Find the first index where 'y' is non-zero
            data = data.loc[first_non_zero_idx:]  # Keep rows from the first non-zero value onward

            # Recheck data validity after cleaning
            if data.empty or data.shape[0] < 2:
                continue

            # Deny processing if non-zero count is below 50 or grand total sales is below 100
            if (data['y'] != 0).sum() < 50:
                continue

            if data['y'].sum() < 100:
                continue

            # Step 4: Generate forecast
            print (f"Generating forecast for ASIN {asin} and Sales Channel {sales_channel}...")
            forecast, forecast_plot = generate_forecast_neuralprophet(data, forecast_days=180)

            # Save the forecast plot
            #output_dir = "/var/www/iwapim/public/tmp/forecast"
            #os.makedirs(output_dir, exist_ok=True)  # Ensure the directory exists
            #output_path = os.path.join(output_dir, f"{asin}_{sales_channel}.png")
            #forecast_plot.savefig(output_path)

            # Step 5: Insert forecast into the database
            insert_forecast_data(forecast, asin, sales_channel, yaml_path)

        except Exception as e:
            print(f"\nError processing ASIN {asin} and Sales Channel {sales_channel}: {e}")
            # Debugging outputs
            print(data.head(10))
            print("Column names:", data.columns)
            print("Row counts:", data.count())
            print(f"Count of NaN values in 'y': {data['y'].isnull().sum()}")
            print(f"Count of zero values in 'y': {(data['y'] == 0).sum()}")
            print(f"Sum of 'y' values: {data['y'].sum()}")
            break

    print("\nForecasting pipeline completed.")

# Entry point for the script
if __name__ == "__main__":
    yaml_path = '/var/www/iwapim/config/local/database.yaml'

    # Parse command-line arguments
    args = sys.argv[1:]
    if '--eu' in args:
        scenario = 1  # Process Amazon.eu only
    elif '--all' in args:
        scenario = 2  # Process sales_channel = 'all' only
    elif '--us' in args:
        scenario = 3  # Process Amazon.com only
    elif '--others' in args:
        scenario = 4  # Process all other than Amazon.eu, Amazon.com, and 'all'
    else:
        scenario = 5  # Process all channels without filter

    # Run the pipeline with the selected scenario
    run_forecast_pipeline(yaml_path, scenario)