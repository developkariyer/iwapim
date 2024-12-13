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

    ramadan_dates = pd.DataFrame({
        'holiday': 'ramadan',
        'ds': pd.to_datetime(['2024-03-10', '2024-04-08', '2025-02-28']),  # Adjust with actual start dates
        'lower_window': 0,
        'upper_window': 29  # Assuming Ramadan lasts 30 days
    })

    three_holy_months = pd.DataFrame({
        'holiday': 'three_holy_months',
        'ds': pd.to_datetime(['2024-02-10', '2025-01-30']),  # Start of the 3 months
        'lower_window': 0,
        'upper_window': 89  # Length of the three holy months
    })

    christmas_dates = pd.DataFrame({
      'holiday': ['christmas', 'christmas_eve', 'new_year', 'christmas_season'] * 3,
      'ds': pd.to_datetime([
          '2024-12-25', '2024-12-24', '2024-12-31', '2024-12-01',  # Christmas season starts December 1
          '2025-12-25', '2025-12-24', '2025-12-31', '2025-12-01',  # Repeat for next year
          '2026-12-25', '2026-12-24', '2026-12-31', '2026-12-01'   # Extend if necessary
      ]),
      'lower_window': [0, 0, 0, -24],  # Adjust the season to start 24 days before
      'upper_window': [0, 0, 1, 25]  # Christmas season lasts till Dec 25
    })

    holidays = pd.concat([ramadan_dates, three_holy_months, christmas_dates])

    model = Prophet(yearly_seasonality=True, weekly_seasonality=False, holidays=holidays)

    # Add custom seasonality
    model.add_seasonality(name='Ramadan', period=354.37, fourier_order=5)
    model.add_seasonality(name='Christmas_Season', period=365.25, fourier_order=5)

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

    future_forecast['yhat'] = future_forecast.apply(
        lambda row: 0 if row['yhat'] < 0 else int(math.ceil(max(0, row['yhat']))),
        axis=1
    )

    # Return only 'ds' and 'yhat'
    return future_forecast[['ds', 'yhat']]
