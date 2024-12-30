class CanvasDrawTool {
    constructor(imageId, $wire) {
        this.imageId = imageId;
        this.$wire = $wire;
        this.savedState = {};
        this.autoSaveTimer = null;

        this.canvas = document.getElementById(`canvas-${imageId}`);
        this.toolModes = document.querySelectorAll(
            `[name="tool-mode-${imageId}"]`
        );
        this.brushSizeRange = document.getElementById(`brush-size-${imageId}`);
        this.maskOpacityRange = document.getElementById(`opacity-${imageId}`);
        this.saveMaskBtn = document.getElementById(`save-btn-${imageId}`);

        this.initCanvas();
        this.initEventListeners();
    }

    initCanvas() {
        this.canvas.isEraser = false;
        this.canvas.brushSize = parseInt(this.brushSizeRange.value, 10);
        this.canvas.customCursor = document.getElementById(
            `custom-cursor-${this.imageId}`
        );
        this.canvas.style.opacity = +this.maskOpacityRange.value / 100;

        this.initCanvasCursor();
        this.loadImageAndMask();
    }

    initCanvasCursor() {
        this.canvas.customCursor.style.width = `${this.canvas.brushSize}px`;
        this.canvas.customCursor.style.height = `${this.canvas.brushSize}px`;
        this.canvas.customCursor.style.background = this.canvas.isEraser
            ? "black"
            : "white";
    }

    initEventListeners() {
        this.toolModes.forEach((tool) => {
            tool.addEventListener("change", (e) => {
                this.canvas.isEraser = e.target.value === "eraser";
                this.initCanvasCursor();
            });
        });

        this.brushSizeRange.addEventListener("input", (e) => {
            this.canvas.brushSize = parseInt(e.target.value, 10);
            this.initCanvasCursor();
        });

        this.maskOpacityRange.addEventListener("input", (e) => {
            this.canvas.style.opacity = +e.target.value / 100;
        });

        this.saveMaskBtn.addEventListener("click", () => this.saveMask());

        this.initCanvasDrawingEvents();
    }

    startAutoSaveTimer() {
        clearTimeout(this.autoSaveTimer);
        this.autoSaveTimer = setTimeout(() => this.saveMask(), 10e3);
    }

    saveMask() {
        clearTimeout(this.autoSaveTimer);
        this.saveCanvasState();
        this.saveMaskBtn.style.display = "none";

        const save = async () => {
            const base64 = this.canvas.toDataURL();
            await this.$wire.saveMask(base64);
            this.updateMask(base64);
        };
        save();
    }

    initCanvasDrawingEvents() {
        const ctx = this.canvas.getContext("2d");
        let drawing = false;
        let lastX = 0;
        let lastY = 0;

        this.canvas.addEventListener("mouseenter", () => {
            this.canvas.customCursor.style.display = "block";
        });

        this.canvas.addEventListener("mouseleave", () => {
            this.canvas.customCursor.style.display = "none";
        });

        this.canvas.addEventListener("mousedown", (e) => {
            drawing = true;
            this.startAutoSaveTimer();

            const { x, y } = this.getCoords(e);

            this.saveMaskBtn.style.display = "block";

            ctx.fillStyle = this.canvas.isEraser ? "black" : "white";
            ctx.beginPath();
            ctx.arc(x, y, this.canvas.brushSize / 2, 0, Math.PI * 2);
            ctx.fill();

            lastX = x;
            lastY = y;
        });

        document.addEventListener("mouseup", () => {
            drawing = false;
            ctx.beginPath();
        });

        this.canvas.addEventListener("mousemove", (e) => {
            this.moveCursor(e.clientX, e.clientY);

            if (!drawing) return;

            this.startAutoSaveTimer();

            const { x, y } = this.getCoords(e);

            ctx.lineWidth = this.canvas.brushSize;
            ctx.lineCap = "round";
            ctx.strokeStyle = this.canvas.isEraser ? "black" : "white";

            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(x, y);
            ctx.stroke();

            lastX = x;
            lastY = y;
        });
    }

    saveCanvasState() {
        this.savedState = {
            brushSize: this.canvas.brushSize,
            isEraser: this.canvas.isEraser,
            opacity: this.canvas.style.opacity,
        };
    }

    restoreCanvasState() {
        if (this.savedState) {
            this.canvas.brushSize = this.savedState.brushSize;
            this.canvas.isEraser = this.savedState.isEraser;
            this.canvas.style.opacity = this.savedState.opacity;

            this.brushSizeRange.value = this.canvas.brushSize;
            this.maskOpacityRange.value =
                parseFloat(this.canvas.style.opacity) * 100;

            this.initCanvasCursor();
        }
    }

    moveCursor(clientX, clientY) {
        this.canvas.customCursor.style.display = "block";

        const rect = this.canvas.getBoundingClientRect();
        const scaleX = this.canvas.width / rect.width;
        const brushSize = this.canvas.brushSize / scaleX;

        const offsetX = clientX - rect.left - brushSize / 2;
        const offsetY = clientY - rect.top - brushSize / 2;

        this.canvas.customCursor.style.width = `${brushSize}px`;
        this.canvas.customCursor.style.height = `${brushSize}px`;
        this.canvas.customCursor.style.left = `${offsetX}px`;
        this.canvas.customCursor.style.top = `${offsetY}px`;
    }

    getCoords(e) {
        const rect = this.canvas.getBoundingClientRect();
        const scaleX = this.canvas.width / rect.width;
        const scaleY = this.canvas.height / rect.height;

        return {
            x: (e.clientX - rect.left) * scaleX,
            y: (e.clientY - rect.top) * scaleY,
        };
    }

    updateMask(base64) {
        const image = document.getElementById(`image-${this.imageId}`);

        const ctx = this.canvas.getContext("2d");
        const mask = new Image();

        mask.src = base64;
        mask.onload = () => {
            this.canvas.width = image.naturalWidth;
            this.canvas.height = image.naturalHeight;

            const setCanvasSize = () => {
                const scaledWidth = image.clientWidth;
                const scaledHeight =
                    (image.naturalHeight / image.naturalWidth) * scaledWidth;
                this.canvas.style.width = `${scaledWidth}px`;
                this.canvas.style.height = `${scaledHeight}px`;
            };

            setCanvasSize();

            ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
            ctx.drawImage(mask, 0, 0, this.canvas.width, this.canvas.height);

            this.restoreCanvasState();

            window.addEventListener("resize", setCanvasSize);
        };
    }

    loadImageAndMask() {
        const image = document.getElementById(`image-${this.imageId}`);
        const maskUrl = image.getAttribute("data-mask-url");

        const ctx = this.canvas.getContext("2d");
        const img = new Image();
        const mask = new Image();

        img.src = image.src;
        img.onload = () => {
            this.canvas.width = img.width;
            this.canvas.height = img.height;

            const setCanvasSize = () => {
                const scaledWidth = image.clientWidth;
                const scaledHeight = (img.height / img.width) * scaledWidth;
                this.canvas.style.width = `${scaledWidth}px`;
                this.canvas.style.height = `${scaledHeight}px`;
            };

            setCanvasSize();
            ctx.drawImage(img, 0, 0);

            mask.src = maskUrl;
            mask.onload = () => {
                ctx.drawImage(mask, 0, 0, img.width, img.height);
            };

            window.addEventListener("resize", setCanvasSize);
        };
    }
}
