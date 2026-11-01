import Alpine from 'alpinejs';

// Signature Pad Alpine Component
Alpine.data('signaturePad', (targetInputId = 'signature_data_uri') => ({
    canvas: null,
    ctx: null,
    isDrawing: false,
    hasSignature: false,
    strokeCount: 0,
    targetInput: null,

    init() {
        this.canvas = this.$refs.canvas;
        this.targetInput = document.getElementById(targetInputId);
        if (!this.canvas) return;

        this.ctx = this.canvas.getContext('2d');
        this.resizeCanvas();
        window.addEventListener('resize', () => this.resizeCanvas());

        // Pointer / Touch / Mouse drawing events
        this.canvas.addEventListener('pointerdown', (e) => this.startDrawing(e));
        this.canvas.addEventListener('pointermove', (e) => this.draw(e));
        this.canvas.addEventListener('pointerup', () => this.stopDrawing());
        this.canvas.addEventListener('pointercancel', () => this.stopDrawing());
        this.canvas.addEventListener('pointerleave', () => this.stopDrawing());
    },

    resizeCanvas() {
        if (!this.canvas) return;
        const rect = this.canvas.getBoundingClientRect();
        // Set actual pixel dimensions to match CSS display width
        if (this.canvas.width !== rect.width || this.canvas.height !== rect.height) {
            const tempImage = this.hasSignature ? this.canvas.toDataURL() : null;
            this.canvas.width = rect.width;
            this.canvas.height = rect.height || 220;

            this.ctx.lineWidth = 2.5;
            this.ctx.lineCap = 'round';
            this.ctx.lineJoin = 'round';
            this.ctx.strokeStyle = '#0f172a'; // Slate 900 ink

            if (tempImage) {
                const img = new Image();
                img.onload = () => this.ctx.drawImage(img, 0, 0);
                img.src = tempImage;
            }
        }
    },

    getPos(e) {
        const rect = this.canvas.getBoundingClientRect();
        return {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };
    },

    startDrawing(e) {
        e.preventDefault();
        this.isDrawing = true;
        const pos = this.getPos(e);
        this.ctx.beginPath();
        this.ctx.moveTo(pos.x, pos.y);
        this.strokeCount++;
    },

    draw(e) {
        if (!this.isDrawing) return;
        e.preventDefault();
        const pos = this.getPos(e);
        this.ctx.lineTo(pos.x, pos.y);
        this.ctx.stroke();
        this.hasSignature = true;
    },

    stopDrawing() {
        if (!this.isDrawing) return;
        this.isDrawing = false;
        this.syncInput();
    },

    clear() {
        if (!this.ctx || !this.canvas) return;
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        this.hasSignature = false;
        this.strokeCount = 0;
        if (this.targetInput) {
            this.targetInput.value = '';
        }
    },

    syncInput() {
        if (this.hasSignature && this.canvas && this.targetInput) {
            this.targetInput.value = this.canvas.toDataURL('image/png');
        }
    }
}));

// Quick Catalog Search Alpine Component
Alpine.data('catalogSearch', () => ({
    search: '',
    selectedCategory: 'all',
    matches(category, name) {
        const catMatch = this.selectedCategory === 'all' || this.selectedCategory === category;
        const nameMatch = !this.search || name.toLowerCase().includes(this.search.toLowerCase());
        return catMatch && nameMatch;
    }
}));

window.Alpine = Alpine;
Alpine.start();
