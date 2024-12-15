# utils/json_wrapper.py

import json

def get_json(data):
    """
    Returns a JSON string wrapped with markers.
    """
    result = json.dumps(data)
    return f'--json start-- {result} --json end--'
