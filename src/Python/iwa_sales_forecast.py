import sys
import os
import logging
from multiprocessing import Process
from collections import defaultdict
from database_operations import fetch_pairs, fetch_data, insert_forecast_data, delete_forecast_data
from forecast_generator import generate_forecast, generate_forecast_arima, generate_forecast_neuralprophet

def run_forecast_pipeline(yaml_path, scenario, max_processes=8):
    """
    Executes the complete forecasting pipeline using multiprocessing.

    Args:
        yaml_path (str): Path to the YAML configuration file for MySQL connection.
        max_processes (int): Maximum number of concurrent processes.

    Returns:
        None
    """
    # Step 1: Fetch ASIN/Sales Channel pairs
    print("Fetching ASIN/Sales Channel pairs...")
    pairs = fetch_pairs(yaml_path, scenario)
    print(f"**********************Fetched pairs type: {type(pairs)}")


    if pairs.empty:
        print("No ASIN/Sales Channel pairs found. Exiting...")
        return

    # Initialize progress tracker and active processes
    active_processes = []
    task_queue = pairs.to_dict('records')  # Convert DataFrame to a list of dicts

    while task_queue or active_processes:
        # Monitor active processes
        for p in active_processes:
            if not p.is_alive():  # Remove finished processes
                active_processes.remove(p)

        # Start new processes if below the max limit
        while len(active_processes) < max_processes and task_queue:
            task = task_queue.pop(0)
            asin, sales_channel = task['asin'], task['sales_channel']
            print(f"Starting process for ASIN {asin}, Sales Channel {sales_channel}...")
            process = Process(target=worker_process, args=(asin, sales_channel, yaml_path))
            process.start()
            active_processes.append(process)

    # Ensure all processes complete
    for p in active_processes:
        p.join()

    print("\nForecasting pipeline completed.")


def worker_process(asin, sales_channel, yaml_path, forecast_days=180):
    """
    Worker function to be run as a separate process. Fetches data for a specific ASIN and sales channel,
    processes it, and saves the results.

    Args:
        asin (str): ASIN to process.
        sales_channel (str): Sales channel to process.
        yaml_path (str): Path to the YAML configuration for database connection.
        forecast_days (int): Number of days to forecast. Default is 180.

    Returns:
        None
    """
    try:
        # Step 1: Delete existing forecast data
        delete_forecast_data(asin, sales_channel, yaml_path)

        # Step 2: Fetch historical sales data
        print(f"Fetching data for ASIN {asin}, Sales Channel {sales_channel}...")
        data = fetch_data(asin, sales_channel, yaml_path)

        # Step 3: Validate initial data
        if data.empty or data.dropna().shape[0] < 2:
            print(f"Insufficient data for ASIN {asin}, Sales Channel {sales_channel}. Skipping...")
            return

        if data['y'].sum() == 0:
            print(f"All sales values are zero for ASIN {asin}, Sales Channel {sales_channel}. Skipping...")
            return

        # Step 4: Remove uninterrupted leading zeros
        first_non_zero_idx = data['y'].ne(0).idxmax()  # Find the first non-zero index
        data = data.loc[first_non_zero_idx:]  # Keep rows starting from the first non-zero value

        # Revalidate data after cleaning
        if data.empty or data.shape[0] < 2:
            print(f"No valid data after cleaning for ASIN {asin}, Sales Channel {sales_channel}. Skipping...")
            return

        # Step 5: Deny processing if non-zero count is below 50 or grand total sales is below 100
        if (data['y'] != 0).sum() < 50:
            print(f"Non-zero sales count < 50 for ASIN {asin}, Sales Channel {sales_channel}. Skipping...")
            return

        if data['y'].sum() < 100:
            print(f"Total sales < 100 for ASIN {asin}, Sales Channel {sales_channel}. Skipping...")
            return

        # Step 6: Generate forecast
        print(f"Generating forecast for ASIN {asin}, Sales Channel {sales_channel}...")
        forecast = generate_forecast_neuralprophet(data, forecast_days=forecast_days)

        # Step 7: Insert forecast data into the database
        insert_forecast_data(forecast, asin, sales_channel, yaml_path)
        print(f"Forecast data saved for ASIN {asin}, Sales Channel {sales_channel}.")

    except Exception as e:
        # Log any errors
        logging.error(f"Error processing ASIN {asin}, Sales Channel {sales_channel}: {e}")





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
    run_forecast_pipeline(yaml_path, scenario, 5)


