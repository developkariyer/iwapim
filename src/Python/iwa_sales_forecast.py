import sys, os, logging
from multiprocessing import Process
from collections import defaultdict
from database_operations import fetch_pairs, insert_forecast_data, delete_forecast_data, fetch_group_data
from forecast_generator import generate_forecast_neuralprophet, generate_group_model_neuralprophet
#from darts_forecasts import generate_forecast_xgboost # too slow without GPU


def run_forecast_pipeline(group=None, forecast_days=90, max_processes=8, asin=None, sales_channel=None, iwasku=None):
    print("*Fetch global data from group...")
    data = fetch_group_data(group, sales_channel)
    if data.empty or data.dropna().shape[0] < 2 or data['y'].sum() == 0:
        print("*Insufficient data for group. Exiting...")
        return
    # fit model and return to here. Later on in pipeline, this model will be used to forecast
    print("*Generating group model using NeuralProphet...")
    model = generate_group_model_neuralprophet(data, forecast_days=forecast_days)

    print("*Fetching ASIN/Sales Channel pairs...")
    pairs = fetch_pairs(group, asin, sales_channel, iwasku)
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
            df = data.query("ID == @asin + '_' + @sales_channel")[['ds', 'y', 'ID']].copy()
            if df.empty or df.dropna().shape[0] < 2 or df['y'].sum() == 0:
                print(f"*No data found for ASIN {asin}, Sales Channel {sales_channel}. Skipping...")
                continue
            print(f"*Starting process for ASIN {asin}, Sales Channel {sales_channel}...")
            process = Process(target=worker_process_for_forecast_pipeline, args=(model, asin, sales_channel, df, forecast_days))
            process.start()
            active_processes.append(process)
    for p in active_processes:
        p.join()
    print("\n*Forecasting pipeline completed.")


def worker_process_for_forecast_pipeline(model, asin, sales_channel, df, forecast_days=90):
    try:
        delete_forecast_data(asin, sales_channel)
        print(f"*Generating forecast for ASIN {asin}, Sales Channel {sales_channel} using NeuralProphet...")
        forecast = generate_forecast_neuralprophet(model, df, forecast_days=forecast_days)
        print("*Saving forecast data to database...")
        insert_forecast_data(forecast, asin, sales_channel)
        print(f"*Forecast data saved for ASIN {asin}, Sales Channel {sales_channel}.")
    except Exception as e:
        logging.error(f"*Error processing ASIN {asin}, Sales Channel {sales_channel}: {e}")



# Entry point for the script
if __name__ == "__main__":
    args = sys.argv[1:]

    group = next((arg.split('=')[1] for arg in args if arg.startswith('--group=')), None)
    asin = next((arg.split('=')[1] for arg in args if arg.startswith('--asin=')), None)
    sales_channel = next((arg.split('=')[1] for arg in args if arg.startswith('--channel=')), None)
    iwasku = next((arg.split('=')[1] for arg in args if arg.startswith('--iwasku=')), None)
    print(f"*Running forecast pipeline using group {group}, ASIN {asin}, Sales Channel {sales_channel}, IWASKU {iwasku}...")
    run_forecast_pipeline(group=group, max_processes=6, forecast_days=90, asin=asin, sales_channel=sales_channel, iwasku=iwasku)
