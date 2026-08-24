import sys
import re
import json

DAY_MAP = {
    'M': 'Monday', 'MON': 'Monday', 'MONDAY': 'Monday',
    'T': 'Tuesday', 'TUE': 'Tuesday', 'TUES': 'Tuesday', 'TUESDAY': 'Tuesday',
    'W': 'Wednesday', 'WED': 'Wednesday', 'WEDNESDAY': 'Wednesday',
    'TH': 'Thursday', 'THU': 'Thursday', 'THUR': 'Thursday', 'THURSDAY': 'Thursday',
    'F': 'Friday', 'FRI': 'Friday', 'FRIDAY': 'Friday',
    'S': 'Saturday', 'SAT': 'Saturday', 'SATURDAY': 'Saturday',
    'SU': 'Sunday', 'SUN': 'Sunday', 'SUNDAY': 'Sunday',
    'TBA': 'TBA'
}

def parse_schedule_content(text):
    lines = text.strip().splitlines()
    parsed_classes = []
    
    for line in lines:
        line = line.strip()
        if not line or 'SUBJECT CODE' in line.upper() or 'SUBJECT DESCRIPTION' in line.upper():
            continue
            
        # Split by tab or multiple spaces / delimiter
        if '\t' in line:
            parts = [p.strip() for p in line.split('\t') if p.strip()]
        elif '|' in line:
            parts = [p.strip() for p in line.split('|') if p.strip()]
        else:
            # Match pattern: Section, Code, Description, Day, Time, Professor
            parts = re.split(r'\s{2,}', line)
            
        if len(parts) >= 4:
            # Cases:
            # parts = [Course/Section, Code, Title, Day, Time, Prof] -> len 6
            # parts = [Code, Title, Day, Time, Prof] -> len 5
            # parts = [Code, Title, Day, Time] -> len 4
            
            section = 'AIS 2A'
            code = ''
            title = ''
            day_raw = 'Monday'
            time_raw = '08:00 AM-10:00 AM'
            prof = 'TBA'
            
            if len(parts) >= 6:
                section = parts[0]
                code = parts[1]
                title = parts[2]
                day_raw = parts[3]
                time_raw = parts[4]
                prof = parts[5]
            elif len(parts) == 5:
                # Could be [Code, Title, Day, Time, Prof]
                code = parts[0]
                title = parts[1]
                day_raw = parts[2]
                time_raw = parts[3]
                prof = parts[4]
            elif len(parts) == 4:
                code = parts[0]
                title = parts[1]
                day_raw = parts[2]
                time_raw = parts[3]
                
            # Normalize day
            day_clean = DAY_MAP.get(day_raw.upper(), day_raw.capitalize())
            
            # Split start and end time if present
            start_time = 'TBA'
            end_time = 'TBA'
            if '-' in time_raw:
                t_parts = time_raw.split('-')
                start_time = t_parts[0].strip()
                end_time = t_parts[1].strip()
            elif time_raw.upper() != 'TBA':
                start_time = time_raw
                
            parsed_classes.append({
                "section": section,
                "code": code,
                "title": title,
                "schedule_day": day_clean,
                "start_time": start_time,
                "end_time": end_time,
                "instructor": prof,
                "room": "Room TBA",
                "units": 3.0
            })
            
    return parsed_classes

if __name__ == "__main__":
    if len(sys.argv) < 2:
        input_data = sys.stdin.read()
    else:
        with open(sys.argv[1], 'r', encoding='utf-8', errors='ignore') as f:
            input_data = f.read()
            
    classes = parse_schedule_content(input_data)
    print(json.dumps({"success": True, "count": len(classes), "classes": classes}))
