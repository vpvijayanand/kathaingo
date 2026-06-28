let tooltipEl = null;
let currentTarget = null;
let tooltipTimeout = null;
let hideTimeout = null;

function getTooltipEl() {
    if (!tooltipEl) {
        tooltipEl = document.createElement('div');
        tooltipEl.className = 'kathaingo-tooltip';
        tooltipEl.style.display = 'none';
        document.body.appendChild(tooltipEl);
    }
    return tooltipEl;
}

function showTooltip(target, text) {
    if (hideTimeout) {
        clearTimeout(hideTimeout);
        hideTimeout = null;
    }

    const el = getTooltipEl();
    el.textContent = text;

    // Reset classes
    el.className = 'kathaingo-tooltip';
    el.style.display = 'block';
    el.style.visibility = 'hidden';

    const tooltipWidth = el.offsetWidth;
    const tooltipHeight = el.offsetHeight;

    const targetRect = target.getBoundingClientRect();
    const scrollX = window.pageXOffset || document.documentElement.scrollLeft;
    const scrollY = window.pageYOffset || document.documentElement.scrollTop;
    const viewportWidth = window.innerWidth || document.documentElement.clientWidth;

    const spacing = 8;
    let position = 'top';

    // Collision detection: Check if there's enough space above
    if (targetRect.top - tooltipHeight - spacing < 10) {
        position = 'bottom';
    }

    el.classList.add(`kathaingo-tooltip--${position}`);

    // Calculate vertical position
    let topVal = 0;
    if (position === 'top') {
        topVal = targetRect.top - tooltipHeight - spacing + scrollY;
    } else {
        topVal = targetRect.bottom + spacing + scrollY;
    }

    // Centered horizontally relative to the target
    const targetCenter = targetRect.left + targetRect.width / 2 + scrollX;
    const leftVal = targetRect.left + (targetRect.width - tooltipWidth) / 2 + scrollX;

    // Clamp leftVal to prevent horizontal clipping
    const minLeft = scrollX + 8;
    const maxLeft = scrollX + viewportWidth - tooltipWidth - 8;
    const clampedLeftVal = Math.max(minLeft, Math.min(maxLeft, leftVal));

    // Calculate relative arrow position
    const arrowLeft = targetCenter - clampedLeftVal;
    const clampedArrowLeft = Math.max(12, Math.min(tooltipWidth - 12, arrowLeft));

    // Apply styles
    el.style.top = `${topVal}px`;
    el.style.left = `${clampedLeftVal}px`;
    el.style.setProperty('--arrow-left', `${clampedArrowLeft}px`);
    el.style.visibility = 'visible';

    // Force reflow for CSS transition
    el.offsetWidth;

    el.classList.add('kathaingo-tooltip--visible');
}

function hideTooltip() {
    clearTimeout(tooltipTimeout);
    
    if (currentTarget) {
        if (currentTarget.hasAttribute('data-tooltip')) {
            const text = currentTarget.getAttribute('data-tooltip');
            currentTarget.setAttribute('title', text);
        }
        currentTarget = null;
    }

    const el = getTooltipEl();
    if (el.classList.contains('kathaingo-tooltip--visible')) {
        el.classList.remove('kathaingo-tooltip--visible');

        if (hideTimeout) clearTimeout(hideTimeout);
        hideTimeout = setTimeout(() => {
            el.style.display = 'none';
        }, 130);
    }
}

// Global Event Delegation
document.addEventListener('mouseover', function(event) {
    const target = event.target.closest('[title], [data-tooltip]');
    if (!target) return;

    // If it has title, move to data-tooltip to suppress native tooltip
    if (target.hasAttribute('title')) {
        const titleText = target.getAttribute('title');
        target.setAttribute('data-tooltip', titleText);
        target.removeAttribute('title');
    }

    const text = target.getAttribute('data-tooltip');
    if (!text || text.trim() === '') return;

    if (currentTarget === target) return;
    currentTarget = target;

    clearTimeout(tooltipTimeout);
    // Display immediately on hover (20ms delay for quick movements)
    tooltipTimeout = setTimeout(() => {
        showTooltip(target, text);
    }, 20);
});

document.addEventListener('mouseout', function(event) {
    if (!currentTarget) return;

    const relatedTarget = event.relatedTarget;
    if (relatedTarget && currentTarget.contains(relatedTarget)) {
        return;
    }

    hideTooltip();
});

document.addEventListener('focusin', function(event) {
    const target = event.target.closest('[title], [data-tooltip]');
    if (!target) return;

    if (target.hasAttribute('title')) {
        const titleText = target.getAttribute('title');
        target.setAttribute('data-tooltip', titleText);
        target.removeAttribute('title');
    }

    const text = target.getAttribute('data-tooltip');
    if (!text || text.trim() === '') return;

    if (currentTarget === target) return;
    currentTarget = target;

    clearTimeout(tooltipTimeout);
    tooltipTimeout = setTimeout(() => {
        showTooltip(target, text);
    }, 20);
});

document.addEventListener('focusout', function(event) {
    if (!currentTarget) return;
    hideTooltip();
});

// Hide tooltips on scroll and resize to keep alignment perfect
window.addEventListener('scroll', hideTooltip, { passive: true });
window.addEventListener('resize', hideTooltip, { passive: true });

// Expose globally for dynamic programmatic interactions
window.KathaingoTooltip = {
    show: function(target, text) {
        currentTarget = target;
        showTooltip(target, text);
    },
    hide: function() {
        hideTooltip();
    },
    getCurrentTarget: function() {
        return currentTarget;
    }
};
