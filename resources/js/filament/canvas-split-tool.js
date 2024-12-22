class CanvasSplitTool {
    constructor(imageId, $wire) {
        this.imageId = imageId;
        this.$wire = $wire;

        this.canvas = document.getElementById(`canvas-${imageId}`);
        this.splitToggle = document.getElementById(`split-toggle-${imageId}`);
        this.angleInput = document.getElementById(`angle-input-${imageId}`);
        this.angleValue = document.getElementById(`angle-value-${imageId}`);
        this.angleContainer = document.getElementById(
            `angle-container-${imageId}`
        );

        this.isSplitMode = false;
        this.ctx = null;
        this.lastMouseX = null; // Save last X position
        this.lastMouseY = null; // Save last Y position

        this.initEventListeners();
    }

    initCanvas() {
        const image = document.getElementById(`image-${this.imageId}`);

        this.canvas.width = image.clientWidth;
        this.canvas.height = image.clientHeight;

        this.originalWidth = image.naturalWidth;
        this.originalHeight = image.naturalHeight;

        this.scaleX = this.originalWidth / this.canvas.width;
        this.scaleY = this.originalHeight / this.canvas.height;

        this.ctx = this.canvas.getContext("2d");
    }

    initEventListeners() {
        this.splitToggle.addEventListener("change", (e) => {
            this.isSplitMode = e.target.checked;
            if (this.isSplitMode) {
                this.canvas.style.display = "block";
                this.angleContainer.style.display = "block";
                this.initCanvas();
            } else {
                this.canvas.style.display = "none";
                this.angleContainer.style.display = "none";
                this.ctx?.clearRect(
                    0,
                    0,
                    this.canvas.width,
                    this.canvas.height
                );
            }
        });

        this.angleInput.addEventListener("input", () => {
            this.angleValue.textContent = this.angleInput.value;

            if (this.isSplitMode && this.lastMouseY !== null) {
                const angle = parseFloat(this.angleInput.value) || 0;
                const centerX = this.canvas.width / 2; // X fixed to center
                this.drawSplitLine(centerX, this.lastMouseY, angle);
            }
        });

        this.canvas.addEventListener("mousemove", (event) => {
            if (!this.isSplitMode) return;

            const rect = this.canvas.getBoundingClientRect();
            this.lastMouseX = event.clientX - rect.left; // Save X position
            this.lastMouseY = event.clientY - rect.top; // Save Y position

            const angle = parseFloat(this.angleInput.value) || 0;
            this.drawSplitLine(this.lastMouseX, this.lastMouseY, angle);
        });

        this.canvas.addEventListener("click", (event) => {
            if (this.isSplitMode) {
                const rect = this.canvas.getBoundingClientRect();
                const x = (event.clientX - rect.left) * this.scaleX;
                const y = (event.clientY - rect.top) * this.scaleY;
                const angle = parseFloat(this.angleInput.value) || 0;

                this.sendSplitData({ x, y, angle });
            }
        });
    }

    drawSplitLine(x, y, angle) {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        this.ctx.strokeStyle = "red";
        this.ctx.lineWidth = 2;

        // Handle vertical line for angle = 90 or 270 degrees
        if (angle == 90 || angle == 270) {
            const centerX = Math.round(x);
            this.ctx.beginPath();
            this.ctx.moveTo(centerX, 0);
            this.ctx.lineTo(centerX, this.canvas.height);
            this.ctx.stroke();
            return;
        }

        // Normal case
        const rad = (angle * Math.PI) / 180;
        const startX = 0;
        const startY = y - x * Math.tan(rad);
        const endX = this.canvas.width;
        const endY = y + (endX - x) * Math.tan(rad);

        this.ctx.beginPath();
        this.ctx.moveTo(startX, startY);
        this.ctx.lineTo(endX, endY);
        this.ctx.stroke();
    }

    sendSplitData(data) {
        this.splitToggle.checked = false;
        this.canvas.style.display = "none";
        this.angleContainer.style.display = "none";
        if (this.ctx) {
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        }

        this.$wire
            .splitImage(this.imageId, data)
            .then(() => {
                this.initCanvas();
                this.canvas.style.display = "none";
            })
            .catch((error) => {
                console.error("Error splitting image:", error);
            });
    }
}
