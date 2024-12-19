import os
import subprocess
import sys
import venv
import shutil

# Конфигурация
VENV_DIR = "venv"  # Папка виртуального окружения
REQUIREMENTS_FILE = "requirements.txt"  # Файл зависимостей

# Цвета для сообщений
class Colors:
    GREEN = "\033[92m"
    RED = "\033[91m"
    RESET = "\033[0m"

def print_success(message):
    print(f"{Colors.GREEN}{message}{Colors.RESET}")

def print_error(message):
    print(f"{Colors.RED}{message}{Colors.RESET}")

def run_command(command):
    """Выполнение shell-команды с обработкой ошибок."""
    try:
        subprocess.run(command, shell=True, check=True)
    except subprocess.CalledProcessError as e:
        print_error(f"Ошибка при выполнении команды: {command}")
        sys.exit(e.returncode)

class RepoManager:
    """Класс для управления репозиториями."""
    def __init__(self, repo_url, target_dirs):
        self.repo_url = repo_url
        self.original_name = repo_url.split("/")[-1].replace(".git", "")
        self.cloned_name = f"{self.original_name}_temp"  # Временное имя
        self.target_dirs = target_dirs  # Список папок для извлечения

    def clone_and_extract(self):
        """Клонирование репозитория и извлечение нужных папок."""
        # Удаление старых данных, если существуют
        if os.path.exists(self.cloned_name):
            print_success(f"Папка {self.cloned_name} уже существует. Удаляю...")
            shutil.rmtree(self.cloned_name)

        print_success(f"Клонирую репозиторий: {self.repo_url}")
        run_command(f"git clone {self.repo_url} {self.cloned_name}")

        # Извлечение нужных папок
        for target_dir in self.target_dirs:
            source_path = os.path.join(self.cloned_name, target_dir)
            if os.path.exists(source_path):
                print_success(f"Извлекаю папку '{target_dir}'...")
                if os.path.exists(target_dir):
                    shutil.rmtree(target_dir)  # Удаляем, если уже есть
                shutil.move(source_path, target_dir)
                print_success(f"Папка '{target_dir}' успешно извлечена.")
            else:
                print_error(f"Папка '{target_dir}' не найдена в репозитории!")

        print_success("Удаляю ненужный репозиторий...")
        shutil.rmtree(self.cloned_name)

def create_virtual_environment():
    """Создание виртуального окружения."""
    if not os.path.exists(VENV_DIR):
        print_success("Создаю виртуальное окружение...")
        venv.create(VENV_DIR, with_pip=True)
        print_success("Виртуальное окружение создано.")
    else:
        print_success("Виртуальное окружение уже существует. Пропускаю.")

def install_pip_tools():
    """Обновление базовых инструментов pip."""
    pip_executable = os.path.join(VENV_DIR, "bin", "pip") if os.name != "nt" else os.path.join(VENV_DIR, "Scripts", "pip.exe")
    print_success("Обновляю pip, setuptools и wheel...")
    run_command(f"{pip_executable} install --upgrade pip setuptools wheel")

def install_requirements():
    """Установка зависимостей из requirements.txt."""
    if not os.path.exists(REQUIREMENTS_FILE):
        print_error(f"Файл {REQUIREMENTS_FILE} не найден.")
        sys.exit(1)

    pip_executable = os.path.join(VENV_DIR, "bin", "pip") if os.name != "nt" else os.path.join(VENV_DIR, "Scripts", "pip.exe")
    print_success("Устанавливаю зависимости из requirements.txt...")
    run_command(f"{pip_executable} install -r {REQUIREMENTS_FILE}")

def main():
    print_success("Начинаю процесс установки...")

    # Шаг 1: Создание виртуального окружения
    create_virtual_environment()

    # Шаг 2: Установка pip инструментов
    install_pip_tools()

    # Шаг 3: Установка зависимостей
    install_requirements()

    # Шаг 4: Управление репозиториями
    repos = [
        {"url": "https://github.com/nagadomi/nunif.git", "targets": ["waifu2x", "nunif"]},
    ]

    for repo in repos:
        manager = RepoManager(repo["url"], repo["targets"])
        manager.clone_and_extract()

    print_success("Установка завершена успешно!")

if __name__ == "__main__":
    main()
