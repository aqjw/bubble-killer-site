export const initials = (string) => {
    return string
        .match(/(\b\S)?/g)
        .join("")
        .match(/(^\S|\S$)?/g)
        .join("");
};

export const scrollToElement = (element, duration) => {
    if (!element) {
        return;
    }

    const start = window.scrollY;
    const target =
        element.getBoundingClientRect().top +
        window.scrollY -
        window.innerHeight / 2 +
        element.offsetHeight / 2;
    const distance = target - start;
    const startTime = performance.now();

    function scrollAnimation(currentTime) {
        const elapsedTime = currentTime - startTime;
        const progress = Math.min(elapsedTime / duration, 1);
        window.scrollTo(0, start + distance * progress);

        if (elapsedTime < duration) {
            requestAnimationFrame(scrollAnimation);
        }
    }

    requestAnimationFrame(scrollAnimation);
};

export const handleResponseError = (response) => {
    const data = response?.data;
    const status = response?.status;

    if (!data || !data.message || !status) {
        return "unknown error";
    }

    // TODO:
    // status === 401
    // status === 403
    // status === 422
    // status === 500

    return data.message;
};

export const formatCompactNumber = (number, fractionDigits = 1) => {
    if (number < 1000) {
        return number;
    } else if (number >= 1000 && number < 1_000_000) {
        return (number / 1000).toFixed(fractionDigits) + "K";
    } else if (number >= 1_000_000 && number < 1_000_000_000) {
        return (number / 1_000_000).toFixed(fractionDigits) + "M";
    } else if (number >= 1_000_000_000 && number < 1_000_000_000_000) {
        return (number / 1_000_000_000).toFixed(fractionDigits) + "B";
    } else if (number >= 1_000_000_000_000 && number < 1_000_000_000_000_000) {
        return (number / 1_000_000_000_000).toFixed(fractionDigits) + "T";
    }
};
