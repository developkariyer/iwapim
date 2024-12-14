#from croston import croston
#from statsmodels.tsa.holtwinters import ExponentialSmoothing
#from prophet import Prophet
import pandas as pd
import logging
import math
#from pmdarima import auto_arima
import matplotlib.pyplot as plt
import numpy as np
from neuralprophet import NeuralProphet


def group_sales_data(df, period):
    df = df.sort_values(by='ds', ascending=False).reset_index(drop=True)
    df['group'] = (df.index // period)
    aggregated_df = df.groupby('group').agg({
        'ds': 'first',
        'y': 'sum'
    }).reset_index(drop=True)
    aggregated_df = aggregated_df.rename(columns={'ds': 'ds', 'y': 'y'})
    aggregated_df = aggregated_df.sort_values(by='ds', ascending=True).reset_index(drop=True)
    return aggregated_df


def generate_forecast_using_groups(data, forecast_days=90):
    forecast_weekly = generate_group_forecast_neuralprophet(group_sales_data(data, 7), 'W', 1)
    forecast_monthly = generate_group_forecast_neuralprophet(group_sales_data(data, 30), 'M', 3)
    forecast_7 = forecast_weekly['yhat'].values[0]
    forecast_30 = forecast_monthly['yhat'].values[0]
    forecast_90 = forecast_monthly['yhat'].sum()
    next_day_in_data = data['ds'].max() + pd.Timedelta(days=1)
    future_data = pd.DataFrame({'ds': pd.date_range(start=next_day_in_data, periods=forecast_days, freq='D')})
    future_data['y'] = 0
    future_data.loc[:6, 'y'] = forecast_7 / 7
    future_data.loc[7:29, 'y'] = (forecast_30 - forecast_7) / 23
    future_data.loc[30:, 'y'] = (forecast_90 - forecast_30) / 60
    return future_data


def generate_group_forecast_neuralprophet(data, freq='D', periods=3):
    if data.empty:
        raise ValueError("Input data is empty. Cannot generate a forecast.")
    model = NeuralProphet(
        yearly_seasonality=True,
        weekly_seasonality=False,
        daily_seasonality=False
    )
    if not isinstance(data, pd.DataFrame):
        raise ValueError("Fetched data is not a DataFrame.")
    model.fit(data, freq=freq)
    future = model.make_future_dataframe(data, periods=periods)
    forecast = model.predict(future)
    if 'yhat1' not in forecast.columns:
        raise ValueError("'yhat1' is missing from the forecast data.")
    return forecast[['ds', 'yhat']]



def generate_forecast_neuralprophet(data, forecast_days=90):
    if data.empty:
        raise ValueError("Input data is empty. Cannot generate a forecast.")
    df_events = pd.DataFrame({
        'event': 'ramadan',
        'ds': pd.to_datetime([
            '2022-04-02',  # 1443 Hijri
            '2023-03-23',  # 1444 Hijri
            '2024-03-10',  # 1445 Hijri
            '2025-02-28',  # 1446 Hijri
            '2026-02-17',  # 1447 Hijri
            '2027-02-06',  # 1448 Hijri
        ])
    })
    model = NeuralProphet(
        n_changepoints=10,
        yearly_seasonality=True,
        weekly_seasonality=True,
        daily_seasonality=False,
        seasonality_mode='multiplicative',
    )
    model = model.add_country_holidays(country_name='US')
    model = model.add_events(df_events)
    if isinstance(data, pd.DataFrame):
        print(f"Fetched data columns: {data.columns}")
    else:
        raise ValueError("Fetched data is not a DataFrame.")
    model.fit(data)
    future = model.make_future_dataframe(data, periods=forecast_days)
    forecast = model.predict(future)
    if 'yhat1' not in forecast.columns:
        raise ValueError("'yhat1' is missing from the forecast data.")
    forecast['yhat'] = forecast['yhat1']
    for i in range(len(forecast)):
        if forecast.loc[i, 'yhat'] <= 0:
            day_before = forecast.loc[i - 1, 'yhat'] if i > 0 else 0
            last_year_date = forecast.loc[i, 'ds'] - pd.Timedelta(days=365)
            last_year_prediction = forecast.loc[forecast['ds'] == last_year_date, 'yhat'].values
            last_year_half = last_year_prediction[0] / 2 if len(last_year_prediction) > 0 else 0
            forecast.loc[i, 'yhat'] = (day_before + last_year_half) / 2
    return forecast[['ds', 'yhat']]










def generate_forecast(data, forecast_days=90):
    """
    Generates a 6-month (daily) sales forecast using Prophet for the given data.

    Args:
        data (pd.DataFrame): Historical sales data with columns 'ds' (date) and 'y' (sales quantity).
        forecast_days (int): Number of days to forecast. Default is 90 (6 months).

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




def generate_forecast_arima(data, forecast_days=90):
    """
    Generates a sales forecast using Auto-ARIMA for the given data.

    Args:
        data (pd.DataFrame): Historical sales data with columns:
                             - 'ds': Date (datetime format)
                             - 'y': Sales quantity (numeric)
        forecast_days (int): Number of days to forecast. Default is 90 (6 months).

    Returns:
        Tuple: (future_forecast, forecast_plot_figure)
            - future_forecast (pd.DataFrame): A DataFrame containing forecasted values with columns:
              - 'ds': Date
              - 'yhat': Predicted sales quantity as a non-negative rounded value.
            - forecast_plot_figure (matplotlib.figure.Figure): A Matplotlib figure of the forecast plot.
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
    """




def generate_forecast_ets(data, forecast_days=90):
    """
    Generates a sales forecast using Exponential Smoothing (ETS) for the given data.

    Args:
        data (pd.DataFrame): Historical sales data with columns:
                             - 'ds': Date (datetime format)
                             - 'y': Sales quantity (numeric)
        forecast_days (int): Number of days to forecast. Default is 90 (3 months).

    Returns:
        pd.DataFrame: A DataFrame containing forecasted values with columns:
                      - 'ds': Date
                      - 'yhat': Predicted sales quantity.
    """
    if data.empty:
        raise ValueError("Input data is empty. Cannot generate a forecast.")

    # Ensure 'y' column is numeric and check for missing values
    if data['y'].isnull().any():
        raise ValueError("Input data contains missing values in 'y' column.")

    # Ensure 'ds' is the index
    if 'ds' in data.columns:
        data.set_index('ds', inplace=True)

    # Fit the ETS model
    print("Fitting Exponential Smoothing (ETS) model...")
    model = ExponentialSmoothing(
        data['y'],
        trend='add',
#        seasonal='add',
#        seasonal_periods=355
    )
    model_fit = model.fit()

    # Generate future forecasts
    forecast = model_fit.forecast(steps=forecast_days)

    # Prepare the forecast DataFrame
    future_dates = pd.date_range(start=data.index[-1] + pd.Timedelta(days=1), periods=forecast_days)
    forecast_df = pd.DataFrame({'ds': future_dates, 'yhat': forecast})

    print(f"Forecast DataFrame: {forecast_df.head()}")

    return forecast_df


def generate_forecast_croston(data, forecast_days=90, alpha=0.1, min_non_zero_sales=10):
    """
    Generates a sales forecast using Croston's Method with handling for sparse data.

    Args:
        data (pd.DataFrame): Historical sales data with columns:
                             - 'ds': Date (datetime format)
                             - 'y': Sales quantity (numeric).
        forecast_days (int): Number of days to forecast. Default is 90.
        alpha (float): Smoothing parameter for Croston's method.
        min_non_zero_sales (int): Minimum non-zero sales required to apply Croston's Method.

    Returns:
        pd.DataFrame: A DataFrame containing forecasted values with columns:
                      - 'ds': Date
                      - 'yhat': Predicted sales quantity.
    """
    if data.empty:
        raise ValueError("Input data is empty. Cannot generate a forecast.")

    # Ensure 'y' column contains numeric values
    if not pd.api.types.is_numeric_dtype(data['y']):
        raise ValueError("'y' column must be numeric.")

    # Check if there are enough non-zero sales to apply Croston
    non_zero_sales = (data['y'] > 0).sum()
    if non_zero_sales < min_non_zero_sales:
        print(f"Insufficient non-zero sales ({non_zero_sales}). Defaulting to zero forecast.")
        future_dates = pd.date_range(start=data['ds'].max() + pd.Timedelta(days=1), periods=forecast_days)
        return pd.DataFrame({'ds': future_dates, 'yhat': [0] * forecast_days})

    # Convert the target column to a NumPy array
    demand = data['y'].values

    # Initialize Croston variables
    demand_size, demand_interval, interval = 0, 0, 1

    # Process the data to calculate demand size and interval
    for i in range(len(demand)):
        if demand[i] > 0:  # Non-zero demand
            demand_size = alpha * demand[i] + (1 - alpha) * demand_size
            demand_interval = alpha * interval + (1 - alpha) * demand_interval
            interval = 0  # Reset interval
        interval += 1  # Increment interval for zero demand

    # Calculate the forecast as demand size divided by demand interval
    demand_interval = max(demand_interval, 1)  # Avoid division by zero
    forecast_value = max(demand_size / demand_interval, 0)  # Avoid negative or NaN forecasts

    # Generate future dates
    future_dates = pd.date_range(start=data['ds'].max() + pd.Timedelta(days=1), periods=forecast_days)

    # Prepare the forecast DataFrame
    forecast_df = pd.DataFrame({'ds': future_dates, 'yhat': [forecast_value] * forecast_days})

    # Adjust future forecast based on last year's corresponding period
    for days_range in [7, 30, 90]:
        next_period = forecast_df.loc[:days_range - 1]
        last_year_period = data.loc[data['ds'].isin(future_dates[:days_range] - pd.Timedelta(days=365))]

        if not last_year_period.empty:
            last_year_values = last_year_period['y'].values

            # Check if last year's values exist for the same period
            if next_period['yhat'].sum() > last_year_values.sum() and data['y'].tail(7).sum() == 0:
                forecast_df.loc[:days_range - 1, 'yhat'] = last_year_values

    return forecast_df
