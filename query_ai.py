import sys
import json
import os
import re
from pathlib import Path
import urllib.request

BASE_DIR = Path(__file__).resolve().parent
DOCUMENTS_DIR = BASE_DIR / "documents"

def get_npc_knowledge():
    return {
        "enrollment": "NPC Enrollment is conducted per semester through the official student portal. Students must present their Certificate of Matriculation, validated ID, and meet prerequisite requirements.",
        "attendance": "NPC Connect uses dynamic rotating QR codes for secure classroom attendance. Scans within the first 5 minutes of class are marked 'Present', scans after 5 minutes are marked 'Late', and unrecorded scans remain 'Absent'.",
        "grading": "The NPC Grading System uses a 1.00 to 5.00 scale where 1.00 is Excellent, 1.25 to 1.75 is Superior/Very Good, 2.00 to 2.75 is Good/Fair, 3.00 is Passing, and 5.00 is Failed.",
        "programs": "Navotas Polytechnic College offers Bachelor of Science in Information Systems (BSIS), Bachelor of Science in Business Administration (BSBA with majors in HR, FM, MM), Bachelor of Secondary Education (BSEd), Bachelor of Elementary Education (BEEd), and Associate in Information Systems (AIS).",
        "vision": "Navotas Polytechnic College envisions itself as a premier higher education institution dedicated to producing competent, values-driven, and socially responsible professionals."
    }

def search_local_documents(query):
    query_lower = query.lower()
    results = []
    
    if not DOCUMENTS_DIR.exists():
        return results

    for file_path in DOCUMENTS_DIR.glob("**/*"):
        if not file_path.is_file():
            continue
        try:
            content = file_path.read_text(encoding='utf-8', errors='ignore')
            score = 0
            for word in query_lower.split():
                if len(word) > 2 and word in content.lower():
                    score += content.lower().count(word)
            
            if score > 0:
                # Find best matching snippet
                idx = content.lower().find(query_lower.split()[0])
                snippet_start = max(0, idx - 50)
                snippet_end = min(len(content), idx + 250)
                snippet = content[snippet_start:snippet_end].replace('\n', ' ').strip()
                results.append({
                    "file": file_path.name,
                    "score": score,
                    "snippet": f"...{snippet}..."
                })
        except Exception:
            pass

    results.sort(key=lambda x: x["score"], reverse=True)
    return results[:3]

def load_env():
    env_file = BASE_DIR / ".env"
    if env_file.exists():
        for line in env_file.read_text(encoding="utf-8").splitlines():
            line = line.strip()
            if line and not line.startswith("#") and "=" in line:
                k, v = line.split("=", 1)
                os.environ.setdefault(k.strip(), v.strip())

load_env()

def generate_ai_response(question, role="student"):
    question_lower = question.lower()
    sources = search_local_documents(question)
    doc_context = ""
    
    # Check if documents have strong match
    if sources and sources[0]["score"] > 3:
        doc_context = "\n\nRelevant Institutional Documents:\n" + " ".join([s["snippet"] for s in sources])

    # 1. Try calling OpenRouter (e.g. stealth/ox-alpha) if API key is present
    openrouter_key = os.getenv("OPENROUTER_API_KEY")
    openrouter_model = os.getenv("OPENROUTER_MODEL", "stealth/ox-alpha")
    
    if openrouter_key:
        try:
            sys_msg = (
                f"You are the official NPC Connect AI Assistant for Navotas Polytechnic College. "
                f"The user is a {role}. Provide clear, professional, helpful, accurate, and concise academic assistance."
            )
            if doc_context:
                sys_msg += doc_context
            
            payload = json.dumps({
                "model": openrouter_model,
                "messages": [
                    {"role": "system", "content": sys_msg},
                    {"role": "user", "content": question}
                ],
                "temperature": 0.7,
                "max_tokens": 400
            }).encode('utf-8')
            
            req = urllib.request.Request(
                "https://openrouter.ai/api/v1/chat/completions",
                data=payload,
                headers={
                    "Authorization": f"Bearer {openrouter_key}",
                    "Content-Type": "application/json",
                    "HTTP-Referer": "http://localhost:8000",
                    "X-Title": "NPC Connect"
                }
            )
            with urllib.request.urlopen(req, timeout=10) as response:
                res_json = json.loads(response.read().decode('utf-8'))
                ai_text = res_json['choices'][0]['message']['content'].strip()
                return {
                    "answer": ai_text,
                    "sources": sources,
                    "model": openrouter_model
                }
        except Exception:
            pass

    # 2. Try calling Local LLM on port 8080 if available
    try:
        req_data = json.dumps({
            "model": "local-model",
            "messages": [
                {"role": "system", "content": f"You are the official NPC Connect AI Assistant for Navotas Polytechnic College. The user is a {role}. Provide clear, professional, helpful, and concise academic assistance.{doc_context}"},
                {"role": "user", "content": question}
            ],
            "temperature": 0.7,
            "max_tokens": 300
        }).encode('utf-8')
        
        req = urllib.request.Request("http://127.0.0.1:8080/v1/chat/completions", data=req_data, headers={'Content-Type': 'application/json'})
        with urllib.request.urlopen(req, timeout=3) as response:
            res_json = json.loads(response.read().decode('utf-8'))
            ai_text = res_json['choices'][0]['message']['content'].strip()
            return {
                "answer": ai_text,
                "sources": sources,
                "model": "local-model"
            }
    except Exception:
        pass

    # 3. Intelligent Domain Engine for NPC Connect
    npc = get_npc_knowledge()
    
    if any(k in question_lower for k in ["hi", "hello", "good morning", "good afternoon", "kamusta", "hey"]):
        if role == "faculty" or role == "teacher":
            ans = f"Hello Professor! I am your NPC Faculty Assistant. I can assist you with course planning, exam question generation, attendance summaries, or grading policies. How can I help you today?"
        elif role == "admin":
            ans = f"Greetings, Administrator. I am ready to help manage institutional schedules, faculty assignments, knowledge documents, or student attendance records. What would you like to review?"
        else:
            ans = f"Hello! Welcome to NPC Connect. I am here to help you with your subjects, class schedule, QR attendance, grades, and academic policies. How can I help you today?"
    
    elif any(k in question_lower for k in ["sched", "class", "subject", "time", "room", "ais 2a"]):
        ans = "Your class schedule is automatically loaded in the **Schedule** tab for your enrolled section (e.g. AIS 2A). You can switch between the 7-day visual grid and the official formatted list, which includes a print-ready PDF certificate format."
    
    elif any(k in question_lower for k in ["grade", "gpa", "failed", "passed", "grades", "score", "mark"]):
        ans = f"Academic grades at NPC are strictly confidential and mapped to your authenticated student ID. {npc['grading']} Final grades will appear in your **Academic** portal once computed and published by your subject professors."
    
    elif any(k in question_lower for k in ["attendance", "qr", "late", "absent", "scan"]):
        ans = f"NPC Connect Attendance System: {npc['attendance']} If you encounter phone camera issues, your professor can also verify your attendance manually on the class roster."
    
    elif any(k in question_lower for k in ["course", "program", "bsis", "bsba", "ais", "bsed", "beed"]):
        ans = f"Academic Offerings at Navotas Polytechnic College: {npc['programs']}"
    
    elif any(k in question_lower for k in ["enroll", "admission", "register"]):
        ans = f"Enrollment Guidelines: {npc['enrollment']}"
    
    elif any(k in question_lower for k in ["quiz", "exam", "lesson", "syllabus", "test"]):
        ans = f"Here is a recommended structure for your course topic:\n\n1. **Learning Objective**: Key concepts and expected student outcomes.\n2. **Discussion Points**: Core theoretical foundations with practical examples.\n3. **Formative Assessment**: 5-item conceptual check and practical lab exercise."
    
    else:
        ans = f"Thank you for your question regarding **\"{question}\"**.\n\nNavotas Polytechnic College is dedicated to supporting students and faculty in their academic journey. You may check the **Schedule**, **Academic Records**, or **Documents** repository for official guidelines, or visit the College Registrar and Department Dean."

    return {
        "answer": ans,
        "sources": sources
    }

if __name__ == "__main__":
    if len(sys.argv) > 1:
        raw_input = sys.argv[1]
        try:
            data = json.loads(raw_input)
            q = data.get("question", "")
            r = data.get("role", "student")
        except Exception:
            q = raw_input
            r = "student"
    else:
        q = "Hello"
        r = "student"

    res = generate_ai_response(q, r)
    print(json.dumps(res))
