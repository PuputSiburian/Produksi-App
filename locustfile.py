from locust import HttpUser, task, between

class ProduksiUser(HttpUser):
    wait_time = between(1, 3)
    
    @task(3)
    def dashboard(self):
        with self.client.get("/dashboard", catch_response=True) as response:
            if response.status_code == 200:
                response.success()
            elif response.status_code == 302:
                # Redirect ke login, anggap sukses
                response.success()
            else:
                response.failure(f"Status code: {response.status_code}")
    
    @task(2)
    def produksi_line(self):
        with self.client.get("/produksi-line", catch_response=True) as response:
            if response.status_code == 200:
                response.success()
            elif response.status_code == 302:
                response.success()
            else:
                response.failure(f"Status code: {response.status_code}")
    
    @task(2)
    def produksi_cutting(self):
        with self.client.get("/produksi-cutting", catch_response=True) as response:
            if response.status_code == 200:
                response.success()
            elif response.status_code == 302:
                response.success()
            else:
                response.failure(f"Status code: {response.status_code}")
    
    @task(2)
    def produksi_crimping(self):
        with self.client.get("/produksi-crimping", catch_response=True) as response:
            if response.status_code == 200:
                response.success()
            elif response.status_code == 302:
                response.success()
            else:
                response.failure(f"Status code: {response.status_code}")