from locust import HttpUser, task, between
from bs4 import BeautifulSoup

class ManagerUser(HttpUser):
    wait_time = between(1, 3)
    
    def on_start(self):
        print("=" * 60)
        print("STARTING LOGIN PROCESS")
        print("=" * 60)
        
        # 1. GET login page - ambil CSRF token
        response = self.client.get("/login")
        print(f"1. GET /login: Status={response.status_code}, Size={len(response.text)}")
        
        soup = BeautifulSoup(response.text, 'html.parser')
        csrf_token = None
        
        # Cari CSRF token
        csrf_input = soup.find('input', {'name': '_token'})
        if csrf_input:
            csrf_token = csrf_input.get('value')
        
        print(f"2. CSRF Token: {csrf_token}")
        
        if not csrf_token:
            print("❌ CSRF token not found!")
            return
        
        # 2. Login - FOLLOW REDIRECTS
        login_data = {
            "_token": csrf_token,
            "email": "manager@gmail.com",
            "password": "password123",
            "remember": "on"
        }
        
        print(f"3. POST /login with email: {login_data['email']}")
        
        # IMPORTANT: allow_redirects=True (default) agar redirect diikuti
        login_response = self.client.post("/login", data=login_data, allow_redirects=True)
        
        print(f"4. Login Response: Status={login_response.status_code}")
        print(f"   Final URL: {login_response.url}")
        print(f"   Response Size: {len(login_response.text)}")
        
        # 3. Cek apakah login berhasil - cek dari URL atau konten
        if "dashboard" in login_response.url.lower() or "login" not in login_response.text.lower():
            print("✅ LOGIN SUCCESSFUL!")
            print(f"   Dashboard size: {len(login_response.text)} bytes")
            
            # Simpan session cookies untuk digunakan di task
            self.client.cookies = login_response.cookies
            print(f"   Cookies: {self.client.cookies}")
        else:
            print("❌ LOGIN FAILED!")
            print(f"   Response preview: {login_response.text[:300]}")
        
        print("=" * 60)
    
    @task(3)
    def view_dashboard(self):
        with self.client.get("/dashboard", catch_response=True) as response:
            if response.status_code == 200 and "login" not in response.text.lower():
                response.success()
                print(f"✅ /dashboard: {len(response.text)} bytes")
            else:
                response.failure(f"Failed: Status={response.status_code}, Size={len(response.text)}")
                print(f"❌ /dashboard: Status={response.status_code}, Size={len(response.text)}")
    
    @task(2)
    def view_manager_dashboard(self):
        with self.client.get("/manager-dashboard", catch_response=True) as response:
            if response.status_code == 200 and "login" not in response.text.lower():
                response.success()
                print(f"✅ /manager-dashboard: {len(response.text)} bytes")
            else:
                response.failure(f"Failed: Status={response.status_code}, Size={len(response.text)}")
    
    @task(2)
    def view_produksi_crimping(self):
        with self.client.get("/produksi-crimping", catch_response=True) as response:
            if response.status_code == 200 and "login" not in response.text.lower():
                response.success()
            else:
                response.failure(f"Failed: Status={response.status_code}")
    
    @task(2)
    def view_produksi_cutting(self):
        with self.client.get("/produksi-cutting", catch_response=True) as response:
            if response.status_code == 200 and "login" not in response.text.lower():
                response.success()
            else:
                response.failure(f"Failed: Status={response.status_code}")
    
    @task(2)
    def view_produksi_line(self):
        with self.client.get("/produksi-line", catch_response=True) as response:
            if response.status_code == 200 and "login" not in response.text.lower():
                response.success()
            else:
                response.failure(f"Failed: Status={response.status_code}")
    
    @task(1)
    def export_pdf(self):
        with self.client.get("/manager-export-pdf", catch_response=True) as response:
            if response.status_code == 200 and len(response.content) > 10000:
                response.success()
            else:
                response.failure(f"Failed: Status={response.status_code}")