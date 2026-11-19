export function initializeProfilePhotoCrop() {
    const input = document.getElementById('profile_photo');
    const dialog = document.getElementById('profile-crop-dialog');
    const canvas = document.getElementById('profile-crop-canvas');

    if (!input || !dialog || !canvas) return;

    document.body.append(dialog);

    const context = canvas.getContext('2d');
    const zoomSlider = document.getElementById('profile-crop-zoom');
    const zoomValue = document.getElementById('profile-crop-zoom-value');
    const status = document.getElementById('profile-photo-status');
    const applyButton = document.getElementById('profile-crop-apply');
    const form = input.closest('form');
    const canvasSize = canvas.width;
    let image = null;
    let imageUrl = null;
    let previewUrl = null;
    let acceptedFile = null;
    let pendingCrop = false;
    let zoom = 1;
    let centerX = canvasSize / 2;
    let centerY = canvasSize / 2;
    let pointer = null;

    function imageDimensions() {
        const baseScale = Math.max(canvasSize / image.naturalWidth, canvasSize / image.naturalHeight);
        return {
            width: image.naturalWidth * baseScale * zoom,
            height: image.naturalHeight * baseScale * zoom,
        };
    }

    function draw() {
        if (!image) return;

        const dimensions = imageDimensions();
        centerX = Math.min(dimensions.width / 2, Math.max(canvasSize - dimensions.width / 2, centerX));
        centerY = Math.min(dimensions.height / 2, Math.max(canvasSize - dimensions.height / 2, centerY));
        context.clearRect(0, 0, canvasSize, canvasSize);
        context.drawImage(image, centerX - dimensions.width / 2, centerY - dimensions.height / 2, dimensions.width, dimensions.height);
    }

    function restoreAcceptedFile() {
        const transfer = new DataTransfer();
        if (acceptedFile) transfer.items.add(acceptedFile);
        input.files = transfer.files;
    }

    function closeEditor(restore = true) {
        dialog.classList.add('hidden');
        dialog.classList.remove('flex');
        pendingCrop = false;
        pointer = null;
        if (restore) restoreAcceptedFile();
        if (imageUrl) URL.revokeObjectURL(imageUrl);
        imageUrl = null;
        image = null;
    }

    function openEditor(file) {
        if (imageUrl) URL.revokeObjectURL(imageUrl);
        imageUrl = URL.createObjectURL(file);
        image = new Image();
        image.onload = () => {
            zoom = 1;
            centerX = canvasSize / 2;
            centerY = canvasSize / 2;
            zoomSlider.value = '1';
            zoomValue.textContent = '100%';
            draw();
            dialog.classList.remove('hidden');
            dialog.classList.add('flex');
            document.getElementById('profile-crop-close').focus();
        };
        image.onerror = () => {
            status.textContent = 'Foto tidak dapat dibuka. Silakan pilih file gambar lain.';
            closeEditor();
        };
        image.src = imageUrl;
    }

    input.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!file) return;

        pendingCrop = true;
        if (!file.type.startsWith('image/')) {
            status.textContent = 'Pilih file gambar JPG, PNG, atau WebP.';
            restoreAcceptedFile();
            pendingCrop = false;
            return;
        }

        status.textContent = 'Geser dan atur pembesaran foto sebelum digunakan.';
        openEditor(file);
    });

    zoomSlider.addEventListener('input', () => {
        zoom = Number(zoomSlider.value);
        zoomValue.textContent = `${Math.round(zoom * 100)}%`;
        draw();
    });

    canvas.addEventListener('pointerdown', (event) => {
        if (!image) return;
        pointer = { x: event.clientX, y: event.clientY };
        canvas.setPointerCapture(event.pointerId);
    });

    canvas.addEventListener('pointermove', (event) => {
        if (!pointer) return;
        const bounds = canvas.getBoundingClientRect();
        centerX += (event.clientX - pointer.x) * canvasSize / bounds.width;
        centerY += (event.clientY - pointer.y) * canvasSize / bounds.height;
        pointer = { x: event.clientX, y: event.clientY };
        draw();
    });

    canvas.addEventListener('pointerup', () => { pointer = null; });
    canvas.addEventListener('pointercancel', () => { pointer = null; });

    applyButton.addEventListener('click', () => {
        if (!image) return;
        applyButton.disabled = true;

        canvas.toBlob((blob) => {
            applyButton.disabled = false;
            if (!blob) {
                status.textContent = 'Foto gagal diproses. Silakan coba file lain.';
                return;
            }
            if (blob.size > 2 * 1024 * 1024) {
                status.textContent = 'Hasil foto melebihi 2 MB. Kurangi detail foto atau pilih gambar lain.';
                return;
            }

            acceptedFile = new File([blob], 'foto-profil.jpg', { type: 'image/jpeg', lastModified: Date.now() });
            restoreAcceptedFile();
            status.textContent = 'Foto sudah dipotong menjadi persegi 512 × 512 dan siap disimpan.';

            const avatar = document.querySelector('[data-profile-avatar]');
            const initials = document.querySelector('[data-profile-initials]');
            if (previewUrl) URL.revokeObjectURL(previewUrl);
            previewUrl = URL.createObjectURL(blob);
            avatar.src = previewUrl;
            avatar.classList.remove('hidden');
            initials?.classList.add('hidden');
            closeEditor(false);
        }, 'image/jpeg', 0.9);
    });

    document.getElementById('profile-crop-cancel').addEventListener('click', () => closeEditor());
    document.getElementById('profile-crop-close').addEventListener('click', () => closeEditor());
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) closeEditor();
    });
    dialog.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeEditor();
    });
    form?.addEventListener('submit', (event) => {
        if (pendingCrop) {
            event.preventDefault();
            applyButton.focus();
        }
    });
}