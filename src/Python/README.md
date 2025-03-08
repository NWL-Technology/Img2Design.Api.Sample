# Img 2 Design - WebAPI Background Removal - Sample PYTHON Code

## Prerequisites

Ensure you have the following installed on your system:

- Python 3.7+
- `pip` (Python package manager)

## Installation

### 1. Clone the Repository

```sh
git clone https://github.com/NWL-Technology/Img2Design.Api.Sample.git
cd Img2Design.Api.Sample/src/Python
```

### 2. Create a Virtual Environment (Optional but Recommended)

```sh
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate
```

### 3. Install Required Dependencies

```sh
pip install -r requirements.txt
```

## Configuration

1. **Set Up API Credentials**

   - Open the `background.py` file.
   - Replace `USER_TOKEN = "your_user_token_here"` with your actual API key.

2. **Ensure Sample Image Exists**
   - Place a sample image inside the `sample/` directory.
   - The script looks for `sample/img_sample.jpg` by default. Change `IMAGE_PATH` if needed.

## Running the Script

Run the script using:

```sh
python main.py
```

## Expected Output

- Processed images will be saved in the `outputs/` directory.
- Logs will display src/Python