// ---------------------------------------------------------
// Show a live preview of the selected image before upload
// ---------------------------------------------------------
function previewImage(event) {
    const file = event.target.files[0];
    const previewImg = document.getElementById('previewImg');

    if (!file) {
        previewImg.removeAttribute('src');
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        previewImg.src = e.target.result;
    };
    reader.readAsDataURL(file);
}