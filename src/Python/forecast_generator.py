from prophet import Prophet
import pandas as pd
import logging
import math

def generate_forecast(data, forecast_days=180):
    """
    Generates a 6-month (daily) sales forecast using Prophet for the given data.

    Args:
        data (pd.DataFrame): Historical sales data with columns 'ds' (date) and 'y' (sales quantity).
        forecast_days (int): Number of days to forecast. Default is 180 (6 months).

    Returns:
        pd.DataFrame: A DataFrame containing the forecasted values with columns:
                      - 'ds': Date
                      - 'yhat': Predicted sales as an upper-rounded integer
    """
    if data.empty:
        raise ValueError("Input data is empty. Cannot generate a forecast.")

    # Ensure sales data is non-zero and add a small constant if needed
    #data['y'] = data['y'].apply(lambda x: max(x, 0.1))

    # Initialize the Prophet model
    model = Prophet(yearly_seasonality=True, weekly_seasonality=False)

    # Add custom seasonality for Ramadan (approximately 354 days)
    model.add_seasonality(name='Ramadan', period=354.37, fourier_order=5)

    # Reduce verbosity of underlying logger
    cmdstanpy_logger = logging.getLogger("cmdstanpy")
    cmdstanpy_logger.setLevel(logging.WARNING)
    cmdstanpy_logger.propagate = False
    while cmdstanpy_logger.handlers:
        cmdstanpy_logger.handlers.pop()

    # Fit the model on historical data
    model.fit(data)

    # Create a dataframe for future dates
    future = model.make_future_dataframe(periods=forecast_days, freq='D')

    # Predict future sales
    forecast = model.predict(future)

    # Filter out only future predictions
    future_forecast = forecast[forecast['ds'] > pd.to_datetime(data['ds'].max())].copy()

    # Adjust negative predictions and round yhat to the nearest upper integer
    future_forecast.loc[:, 'yhat'] = future_forecast['yhat'].apply(lambda x: max(math.ceil(x), 1))

    # Return only 'ds' and 'yhat'
    return future_forecast[['ds', 'yhat']]
