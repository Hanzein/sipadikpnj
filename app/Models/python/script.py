import sys
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
import time

def main(nama_lomba):
    # Setup WebDriver
    driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()))
    
    # Mencari di Google
    driver.get("https://www.google.com")
    search_box = driver.find_element(By.NAME, "q")
    search_box.send_keys(nama_lomba)
    search_box.submit()
    
    time.sleep(3)  # Tunggu hasil pencarian

    # Cek apakah nama lomba ada di hasil pencarian
    try:
        results = driver.find_elements(By.XPATH, "//h3")
        for result in results:
            if nama_lomba.lower() in result.text.lower():
                print("Nama Lomba Valid")
                driver.quit()
                return
    except Exception as e:
        print("Error:", e)

    print("Harap Cari Secara Manual")
    driver.quit()

if __name__ == "__main__":
    if len(sys.argv) > 1:
        main(sys.argv[1])
    else:
        print("Nama lomba tidak diberikan.")
