import pandas as pd
from darts import TimeSeries
from darts.models import XGBModel

def generate_forecast_xgboost(data, forecast_days=90):
    if data.empty:
        raise ValueError("Input data is empty. Cannot generate a forecast.")

    if isinstance(data, pd.DataFrame):
        print(f"Fetched data columns: {data.columns}")
    else:
        raise ValueError("Fetched data is not a DataFrame.")

    # Ensure 'ds' is in datetime format
    data['ds'] = pd.to_datetime(data['ds'])

    # Convert data to Darts TimeSeries
    series = TimeSeries.from_dataframe(data, time_col='ds', value_cols='y')

    # Split the data for training and forecasting
    train = series[:-forecast_days]

    # Initialize and fit the XGBoost model
    model = XGBModel(
        input_chunk_length=15,
        output_chunk_length=7,
        lags=[-7]  # Add lagged variables for the model
    )
    model.fit(train)

    # Forecast the future
    forecast = model.predict(n=forecast_days)

    # Convert the forecast to a DataFrame
    forecast_df = forecast.pd_dataframe().reset_index()
    forecast_df.columns = ['ds', 'yhat']

    print(forecast_df)
    return forecast_df
