import sys, os, logging
from multiprocessing import Process
from collections import defaultdict
from database_operations import fetch_pairs, fetch_data, insert_forecast_data, delete_forecast_data
#from forecast_generator import generate_forecast_neuralprophet, generate_forecast_using_groups
from darts_forecasts import generate_forecast_xgboost

def run_forecast_pipeline(yaml_path, max_processes=8, asin=None, sales_channel=None, iwasku=None):
    print("Fetching ASIN/Sales Channel pairs...")
    pairs = fetch_pairs(yaml_path, asin, sales_channel, iwasku)
    if pairs.empty:
        print("No ASIN/Sales Channel pairs found. Exiting...")
        return
    active_processes = []
    task_queue = pairs.to_dict('records')
    while task_queue or active_processes:
        for p in active_processes:
            if not p.is_alive():
                active_processes.remove(p)
        while len(active_processes) < max_processes and task_queue:
            task = task_queue.pop(0)
            asin, sales_channel = task['asin'], task['sales_channel']
            print(f"Starting process for ASIN {asin}, Sales Channel {sales_channel}...")
            process = Process(target=worker_process, args=(asin, sales_channel, yaml_path))
            process.start()
            active_processes.append(process)
    for p in active_processes:
        p.join()
    print("\nForecasting pipeline completed.")


def worker_process(asin, sales_channel, yaml_path, forecast_days=90):
    try:
        delete_forecast_data(asin, sales_channel, yaml_path)
        data = fetch_data(asin, sales_channel, yaml_path)
        if data.empty or data.dropna().shape[0] < 2 or data['y'].sum() == 0:
            print(f"Insufficient data for ASIN {asin}, Sales Channel {sales_channel}. Skipping...")
            return
        '''
        if True: #(data['y'] > 1e-10).sum(axis=0) > 100:
            print(f"Generating forecast for ASIN {asin}, Sales Channel {sales_channel} using NeuralProphet...")
            forecast = generate_forecast_neuralprophet(data, forecast_days=forecast_days)
        else:
            print(f"Generating forecast for ASIN {asin}, Sales Channel {sales_channel} using Grouped Forecast...")
            foreast = generate_forecast_using_groups(data, forecast_days=forecast_days)
        '''
        print(f"Generating forecast for ASIN {asin}, Sales Channel {sales_channel} using XGBoost...")
        forecast = generate_forecast_xgboost(data, forecast_days=forecast_days)
        insert_forecast_data(forecast, asin, sales_channel, yaml_path)
        print(f"Forecast data saved for ASIN {asin}, Sales Channel {sales_channel}.")
    except Exception as e:
        logging.error(f"Error processing ASIN {asin}, Sales Channel {sales_channel}: {e}")


# Entry point for the script
if __name__ == "__main__":
    yaml_path = '/var/www/iwapim/config/local/database.yaml'

    args = sys.argv[1:]
    asin = next((arg.split('=')[1] for arg in args if arg.startswith('--asin=')), None)
    sales_channel = next((arg.split('=')[1] for arg in args if arg.startswith('--channel=')), None)
    iwasku = next((arg.split('=')[1] for arg in args if arg.startswith('--iwasku=')), None)

    run_forecast_pipeline(yaml_path, 8, asin, sales_channel, iwasku)


