from prophet import Prophet
import pandas as pd
import logging
import math
from pmdarima import auto_arima
import matplotlib.pyplot as plt
import numpy as np


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

    # Ramadan (30 days)
    ramadan_dates = pd.DataFrame({
        'holiday': 'ramadan',
        'ds': pd.to_datetime([
            '2022-04-02',  # 1443 Hijri
            '2023-03-23',  # 1444 Hijri
            '2024-03-10',  # 1445 Hijri
            '2025-02-28',  # 1446 Hijri
        ]),
        'lower_window': [-7] * 4,  # Start on the first day
        'upper_window': [29] * 4  # Lasts 30 days
    })

    # Three Holy Months (90 days total)
    three_holy_months = pd.DataFrame({
        'holiday': 'three_holy_months',
        'ds': pd.to_datetime([
            '2022-02-03',  # Start of Rajab, 1443 Hijri
            '2023-01-23',  # Start of Rajab, 1444 Hijri
            '2024-01-11',  # Start of Rajab, 1445 Hijri
            '2025-01-01',  # Start of Rajab, 1446 Hijri
        ]),
        'lower_window': [0] * 4,  # Start on the first day
        'upper_window': [89] * 4  # Covers 3 months (Rajab, Sha'ban, Ramadan)
    })

    # Christmas Season (24 days before and 7 days after)
    christmas_dates = pd.DataFrame({
        'holiday': 'christmas_season',
        'ds': pd.to_datetime([
            '2022-12-25',  # Christmas 2022
            '2023-12-25',  # Christmas 2023
            '2024-12-25',  # Christmas 2024
            '2025-12-25',  # Christmas 2025
        ]),
        'lower_window': [-24] * 4,  # Start 24 days before Christmas
        'upper_window': [7] * 4    # Extend 7 days after Christmas
    })

    # Combine all holidays into a single DataFrame
    holidays = pd.concat([ramadan_dates, three_holy_months, christmas_dates], ignore_index=True)

    #model = Prophet(yearly_seasonality=True, weekly_seasonality=False, holidays=holidays)
    model = Prophet(yearly_seasonality=True, weekly_seasonality=False)

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

    forecast_plot_figure = model.plot(forecast)
    plt.close(forecast_plot_figure)  # Prevent showing the plot in non-interactive environments

    # Return only 'ds' and 'yhat', along with the forecast plot
    return future_forecast[['ds', 'yhat']], forecast_plot_figure




def generate_forecast_arima(data, forecast_days=180):
    """
    Generates a sales forecast using Auto-ARIMA for the given data.

    Args:
        data (pd.DataFrame): Historical sales data with columns:
                             - 'ds': Date (datetime format)
                             - 'y': Sales quantity (numeric)
        forecast_days (int): Number of days to forecast. Default is 180 (6 months).

    Returns:
        Tuple: (future_forecast, forecast_plot_figure)
            - future_forecast (pd.DataFrame): A DataFrame containing forecasted values with columns:
              - 'ds': Date
              - 'yhat': Predicted sales quantity as a non-negative rounded value.
            - forecast_plot_figure (matplotlib.figure.Figure): A Matplotlib figure of the forecast plot.
    """
    if data.empty:
        raise ValueError("Input data is empty. Cannot generate a forecast.")

    # Ensure data is sorted by date
    data = data.sort_values(by='ds')

    # Fit the Auto-ARIMA model
    print("Training Auto-ARIMA model...")
    model = auto_arima(
        data['y'],
        seasonal=True,  # Enable seasonal ARIMA
        m=365,          # Seasonal period (e.g., 365 for yearly seasonality in daily data)
        stepwise=True,
        suppress_warnings=True,
        trace=True      # Set to False to reduce output verbosity
    )

    # Generate future forecasts
    forecast = model.predict(n_periods=forecast_days)

    # Create a DataFrame for future dates
    last_date = pd.to_datetime(data['ds'].max())
    future_dates = [last_date + pd.Timedelta(days=i) for i in range(1, forecast_days + 1)]
    future_forecast = pd.DataFrame({'ds': future_dates, 'yhat': np.maximum(0, np.round(forecast))})

    # Create a forecast plot
    forecast_plot_figure, ax = plt.subplots(figsize=(10, 6))
    ax.plot(data['ds'], data['y'], label="Historical Data", color="blue")
    ax.plot(future_forecast['ds'], future_forecast['yhat'], label="Forecast", color="orange")
    ax.axvline(x=last_date, linestyle="--", color="gray", label="Forecast Start")
    ax.set_title("Auto-ARIMA Sales Forecast")
    ax.set_xlabel("Date")
    ax.set_ylabel("Sales Quantity")
    ax.legend()
    plt.close(forecast_plot_figure)  # Prevent showing the plot in non-interactive environments

    # Return the forecast DataFrame and plot
    return future_forecast, forecast_plot_figure
