.minute-card {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 12px 40px rgba(30, 64, 175, 0.08);
    border: 1px solid #e2e8f0;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    min-height: 100%;
}

.minute-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 55px rgba(30, 64, 175, 0.16);
}

.minute-card-hero {
    position: relative;
    height: 210px;
    background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
}

@media (max-width: 576px) {
    .minute-card-hero {
        height: 180px;
    }
}

.minute-card-viewport {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.minute-card-slide {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0;
    transition: opacity 0.4s ease;
}

.minute-card-slide.is-active {
    opacity: 1;
    position: absolute;
}

.minute-card-placeholder {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #475569;
    gap: 10px;
    font-size: 0.95rem;
}

.minute-card-placeholder i {
    font-size: 2rem;
    color: #1e40af;
}

.minute-card-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: none;
    background: rgba(15, 23, 42, 0.55);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.3s ease;
}

.minute-card-nav:hover {
    background: rgba(15, 23, 42, 0.8);
}

.minute-card-nav:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.4);
}

.minute-card-nav[data-minute-prev] {
    left: 14px;
}

.minute-card-nav[data-minute-next] {
    right: 14px;
}

.minute-card-dots {
    position: absolute;
    bottom: 12px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 8px;
}

.minute-card-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    transition: transform 0.3s ease, background 0.3s ease;
}

.minute-card-dot.is-active {
    transform: scale(1.2);
    background: #ffffff;
}

.minute-card-content {
    padding: 28px 28px 24px;
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.minute-card-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 0.95rem;
    color: #4a5568;
}

.minute-card-meta .minute-card-date {
    font-weight: 600;
    color: #1e40af;
}

.minute-card-meta .minute-card-location {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(30, 64, 175, 0.1);
    color: #1e40af;
    border-radius: 999px;
    padding: 4px 12px;
    font-size: 0.85rem;
}

.minute-card-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1a202c;
}

.minute-card-summary {
    color: #4a5568;
    line-height: 1.7;
}

.minute-card-section-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #1e40af;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.minute-card-section-title-inline {
    display: block;
    margin-bottom: 12px;
}

.minute-card-documents {
    display: grid;
    gap: 10px;
}

.minute-card-documents a {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: #1e40af;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s ease;
}

.minute-card-documents a:hover {
    color: #172554;
}

.minute-card-documents i {
    color: #ef4444;
}

.minute-card-gallery-thumbs {
    display: flex;
    flex-wrap: wrap;
    margin: -4px;
}

.minute-card-gallery-thumbs a {
    display: block;
    width: 72px;
    height: 72px;
    margin: 4px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 18px rgba(30, 64, 175, 0.12);
}

.minute-card-gallery-thumbs img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.minute-card-footer {
    margin-top: auto;
}
