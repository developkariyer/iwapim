yaml_path = '/var/www/iwapim/config/local/database.yaml'

output_path = '/var/www/iwapim/tmp/forecasts'

islamic_events = pd.DataFrame({
    "event": "ramadan",
    "ds": pd.to_datetime([
        "2022-04-02",  # 1443 Hijri
        "2023-03-23",  # 1444 Hijri
        "2024-03-10",  # 1445 Hijri
        "2025-02-28",  # 1446 Hijri
    ]),
    "lower_window": -7,
    "upper_window": 29
})
