<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <title>Add Announcement</title>
    <link rel="stylesheet" href="{{ asset('css/C_TaskBar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/C_Announcement.css') }}">
</head>
<body>
    <x-taskbar />
    <section class="announcement-form">
        <div class="title-schedule">
            <h1>ANNOUNCEMENT CONTENT</h1>
        </div>

        <textarea id="announcementText" placeholder="Enter your announcement here..."></textarea>
        
        <div class="format-options">
            <label for="colorPicker">Text Color:</label>
            <input type="color" id="colorPicker">
            
            <label for="boldCheckbox">Bold:</label>
            <input type="checkbox" id="boldCheckbox">
            
            <label for="italicCheckbox">Italic:</label>
            <input type="checkbox" id="italicCheckbox">
            
            <label for="underlineCheckbox">Underline:</label>
            <input type="checkbox" id="underlineCheckbox" />

            <div class="upload-image">
                <label for="imageUpload">Upload Image:</label>
                <input type="file" id="imageUpload" accept="image/*">
            </div>
        </div>

        <button onclick="saveAnnouncement()">SAVE ANNOUNCEMENT</button>
        
        <div id="notification-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; z-index: 9999;">
            <div style="background-color: white; padding: 20px; border-radius: 8px; max-width: 400px; text-align: center;">
                <p id="notification-message" style="font-size: 1.2rem; color: black;"></p>
            </div>
        </div>
    </section>    

    <footer>
        <p>&copy; 2024 SUKAD Event Management</p>
    </footer>

    <script>
        function displayNotification(message, delay = 3000) {
            return new Promise((resolve) => {
                const modal = document.getElementById('notification-modal');
                const messageElement = document.getElementById('notification-message');

                messageElement.textContent = message;
                modal.style.display = 'flex';

                setTimeout(() => {
                    closeNotification();
                    resolve(); 
                }, delay);
            });
        }

        function closeNotification() {
            const modal = document.getElementById('notification-modal');
            modal.style.display = 'none';
        }

        function saveAnnouncement() {
            const content = document.getElementById('announcementText').value;
            const color = document.getElementById('colorPicker').value;
            const bold = document.getElementById('boldCheckbox').checked;
            const italic = document.getElementById('italicCheckbox').checked;
            const underline = document.getElementById('underlineCheckbox').checked;
            const image = document.getElementById('imageUpload').files[0]; 

            const formData = new FormData();
            formData.append('content', content);
            formData.append('color', color);
            formData.append('bold', bold);
            formData.append('italic', italic);
            formData.append('underline', underline);
            if (image) {
                formData.append('image', image);
            }

            fetch("{{ route('saveAnnouncement') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData 
            })
            .then(response => response.json())
            .then(data => {
                showNotification(data.message); 
            })
            .catch(error => {
                console.error('Error saving announcement:', error);
                showNotification('Error saving announcement.'); 
            });

            displayNotification("Saving your announcement...", 3000)
                .then(() => {
                    return fetch("{{ route('saveAnnouncement') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData 
                    });
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message === 'Announcement saved successfully!') {
                        return displayNotification('Announcement saved successfully!', 3000);
                    } else {
                        return displayNotification('Failed to save announcement.', 3000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    return displayNotification('Error saving announcement.', 3000);
                });
        }

    </script>
</body>
</html>