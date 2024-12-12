from prophet import Prophet
import pandas as pd
import logging

def generate_forecast(data, forecast_days=180):
    """
    Generates a 6-month (daily) sales forecast using Prophet for the given data.

    Args:
        data (pd.DataFrame): Historical sales data with columns 'ds' (date) and 'y' (sales quantity).
        forecast_days (int): Number of days to forecast. Default is 180 (6 months).

    Returns:
        pd.DataFrame: A DataFrame containing the forecasted values with columns:
                      - 'ds': Date
                      - 'yhat': Predicted sales
                      - 'yhat_lower': Lower bound of prediction
                      - 'yhat_upper': Upper bound of prediction
    """
    if data.empty:
        raise ValueError("Input data is empty. Cannot generate a forecast.")

    # Initialize the Prophet model
    model = Prophet()

    cmdstanpy_logger = logging.getLogger("cmdstanpy")
    cmdstanpy_logger.setLevel(logging.WARNING)
    cmdstanpy_logger.propagate = False
    while cmdstanpy_logger.handlers:
        cmdstanpy_logger.handlers.pop()


    # Fit the model on historical data
    model.fit(data)

    # Create a future DataFrame for the next 6 months
    future = model.make_future_dataframe(periods=forecast_days)

    # Generate the forecast
    forecast = model.predict(future)

    # Filter forecasted data for the next 6 months
    forecasted_data = forecast[['ds', 'yhat', 'yhat_lower', 'yhat_upper']].iloc[-forecast_days:]
    return forecasted_data
