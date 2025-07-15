import sys
import psycopg2
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from webdriver_manager.chrome import ChromeDriverManager

def fetch_competition_names():
    # Koneksi ke PostgreSQL
    conn = psycopg2.connect(
        dbname="nama_database",
        user="nama_user",
        password="password",
        host="localhost"
    )
    cur = conn.cursor()
    cur.execute("SELECT nama_lomba FROM tabel_lomba")
    competition_names = [row[0] for row in cur.fetchall()]
    cur.close()
    conn.close()
    return competition_names

def validate_competition_name(name):
    chrome_options = Options()
    chrome_options.add_argument("--headless")  # Jalankan Chrome tanpa GUI
    driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=chrome_options)
    driver.get("https://www.google.com")
    search_box = driver.find_element(By.NAME, "q")
    search_box.send_keys(name)
    search_box.send_keys(Keys.RETURN)
    results = driver.find_elements(By.CSS_SELECTOR, "h3")
    driver.quit()
    return any(name.lower() in result.text.lower() for result in results)

def main():
    competition_names = fetch_competition_names()
    results = {}
    for name in competition_names:
        if validate_competition_name(name):
            results[name] = "Nama Lomba Valid"
        else:
            results[name] = "Harap Cari Secara Manual"
    print(results)

if _name_ == "_main_":
    main()