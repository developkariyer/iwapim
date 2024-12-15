import yaml
from config import yaml_path

def get_mysql_config():
    """
    Parses the Pimcore YAML configuration file to extract MySQL connection details.

    Args:
        yaml_path (str): Path to the Pimcore YAML configuration file.

    Returns:
        dict: A dictionary containing MySQL connection details.
    """
    try:
        with open(yaml_path, 'r') as file:
            config = yaml.safe_load(file)

        # Navigate the nested structure to extract database configuration
        db_config = config.get('doctrine', {}).get('dbal', {}).get('connections', {}).get('default', {})
        if not db_config:
            raise ValueError("Database configuration not found in YAML file.")

        # Return MySQL connection details
        return {
            'host': db_config.get('host', 'localhost'),
            'port': db_config.get('port', 3306),
            'user': db_config.get('user'),
            'password': db_config.get('password'),
            'database': db_config.get('dbname'),
        }

    except Exception as e:
        print(f"Error parsing YAML file: {e}")
        raise
