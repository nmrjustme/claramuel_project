<!DOCTYPE html>
<html>
<head>
  <title>Vision API Image Analysis</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 2em;
    }
    img {
      max-width: 300px;
      margin-top: 1em;
    }
    ul {
      padding-left: 1em;
    }
  </style>
</head>
<body>
  <h2>Image Analysis with Google Vision API</h2>

  <input type="file" id="imageInput" accept="image/*" />
  <button onclick="analyzeImage()">Analyze Image</button>

  <div id="imagePreview"></div>
  <h3>Labels:</h3>
  <ul id="labelList"></ul>

  <script>
    async function analyzeImage() {
      const fileInput = document.getElementById('imageInput');
      const file = fileInput.files[0];
      if (!file) {
        alert("Please choose an image.");
        return;
      }

      // Show image preview
      const imagePreview = document.getElementById('imagePreview');
      const reader = new FileReader();
      reader.onloadend = async () => {
        const base64Image = reader.result;
        imagePreview.innerHTML = `<img src="${base64Image}" alt="Preview Image">`;

        const base64 = base64Image.replace(/^data:image\/[a-z]+;base64,/, '');

        // Call Google Vision API
        const response = await fetch(
          'https://vision.googleapis.com/v1/images:annotate?key=AIzaSyDmtFXhUQYJME4b0466F8ieMUpnH5hoWRU',
          {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              requests: [
                {
                  image: { content: base64 },
                  features: [{ type: 'LABEL_DETECTION', maxResults: 10 }]
                }
              ]
            })
          }
        );

        const result = await response.json();
        const labels = result.responses[0].labelAnnotations;

        // Display labels in a list
        const labelList = document.getElementById('labelList');
        labelList.innerHTML = ''; // Clear previous results

        if (labels) {
          labels.forEach(label => {
            const item = document.createElement('li');
            item.textContent = `${label.description} (${(label.score * 100).toFixed(1)}%)`;
            labelList.appendChild(item);
          });
        } else {
          labelList.innerHTML = '<li>No labels found.</li>';
        }
      };

      reader.readAsDataURL(file);
    }
  </script>
</body>
</html>
