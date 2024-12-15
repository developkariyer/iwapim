import sys, os, logging
from multiprocessing import Process
from collections import defaultdict
from database_operations import fetch_pairs, fetch_data, insert_forecast_data, delete_forecast_data, fetch_groups, fetch_group_data
from forecast_generator import generate_forecast_neuralprophet, generate_group_model_neuralprophet
from config import yaml_path
#from darts_forecasts import generate_forecast_xgboost # too slow without GPU


def run_groups_forecast_pipeline(yaml_path):
    print("*Fetching groups...")
    groups = fetch_groups(yaml_path)
    print(f"*Found {len(groups)} groups.")
    if not groups:
        print("*No groups found. Exiting...")
        return
    for group_id in groups:
        print(f"*Processing group {group_id}...")
        data = fetch_group_data(group_id, yaml_path)
        if data.empty or data.dropna().shape[0] < 2 or data['y'].sum() == 0:
            print(f"*Insufficient data for group {group_id}. Skipping...")
            continue
        print(f"*Generating forecast for group {group_id} using NeuralProphet...")
        generate_group_model_neuralprophet(data, group_id)
        print(f"*Forecast model saved for group {group_id}.")



def run_forecast_pipeline(yaml_path, max_processes=8, asin=None, sales_channel=None, iwasku=None):
    print("*Fetching ASIN/Sales Channel pairs...")
    pairs = fetch_pairs(yaml_path, asin, sales_channel, iwasku)
    if pairs.empty:
        print("*No ASIN/Sales Channel pairs found. Exiting...")
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
            print(f"*Starting process for ASIN {asin}, Sales Channel {sales_channel}...")
            process = Process(target=worker_process_for_forecast_pipeline, args=(asin, sales_channel, yaml_path))
            process.start()
            active_processes.append(process)
    for p in active_processes:
        p.join()
    print("\n*Forecasting pipeline completed.")


def worker_process_for_forecast_pipeline(asin, sales_channel, yaml_path, forecast_days=90):
    try:
        delete_forecast_data(asin, sales_channel, yaml_path)
        data = fetch_data(asin, sales_channel, yaml_path)
        if data.empty or data.dropna().shape[0] < 2 or data['y'].sum() == 0:
            print(f"*Insufficient data for ASIN {asin}, Sales Channel {sales_channel}. Skipping...")
            return
        print(f"*Generating forecast for ASIN {asin}, Sales Channel {sales_channel} using NeuralProphet...")
        forecast = generate_forecast_neuralprophet(data, forecast_days=forecast_days)
        insert_forecast_data(forecast, asin, sales_channel, yaml_path)
        print(f"*Forecast data saved for ASIN {asin}, Sales Channel {sales_channel}.")
    except Exception as e:
        logging.error(f"*Error processing ASIN {asin}, Sales Channel {sales_channel}: {e}")



# Entry point for the script
if __name__ == "__main__":
    args = sys.argv[1:]
    groups_flag = any(arg == '--groups' for arg in args)  # Check for --groups flag
    if groups_flag:
        # Run group-based training/forecasting
        print("*Running group-based forecast pipeline")
        run_groups_forecast_pipeline(yaml_path)
    else:
        # Default to product-based processing
        asin = next((arg.split('=')[1] for arg in args if arg.startswith('--asin=')), None)
        sales_channel = next((arg.split('=')[1] for arg in args if arg.startswith('--channel=')), None)
        iwasku = next((arg.split('=')[1] for arg in args if arg.startswith('--iwasku=')), None)
        print("*Running product-based forecast pipeline")
        run_forecast_pipeline(yaml_path, 8, asin, sales_channel, iwasku)

