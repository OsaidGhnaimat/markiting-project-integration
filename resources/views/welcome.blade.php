<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Additional CSS for styling */
        #image-preview {
            max-width: 50%;
            height: auto;
        }
    </style>
</head>
<body>

@if ($errors->any())
<div class="alert alert-danger col-8 m-auto" >
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form action="{{ route('giminipro') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="image">Image:</label>
                    <input type="file" class="form-control-file" name="image" id="image-input">
                    <!-- Preview image element -->
                    <img id="image-preview" src="#" alt="Image Preview" class="d-none mt-2">
                </div>

                <div class="form-group">
                    <label for="text">Text:</label>
                    <textarea class="form-control" name="text" id="text" cols="30" rows="10"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>

<!-- Include Bootstrap JS (optional) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    // JavaScript to display image preview after selecting a file
    const imageInput = document.getElementById('image-input');
    const imagePreview = document.getElementById('image-preview');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];

        if (file) {
            const reader = new FileReader();

            reader.addEventListener('load', function() {
                imagePreview.src = reader.result;
                imagePreview.classList.remove('d-none'); // Show the image preview
            });

            reader.readAsDataURL(file);
        }
    });
</script>
</body>
</html>
