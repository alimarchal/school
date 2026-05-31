type SoundKind = 'success' | 'error';

function playTone(kind: SoundKind): void {
    if (typeof window === 'undefined') {
        return;
    }

    const AudioContextClass = window.AudioContext || window.webkitAudioContext;

    if (!AudioContextClass) {
        return;
    }

    const context = new AudioContextClass();
    const gain = context.createGain();
    const first = context.createOscillator();
    const second = context.createOscillator();
    const now = context.currentTime;

    gain.connect(context.destination);
    gain.gain.setValueAtTime(0.0001, now);
    gain.gain.exponentialRampToValueAtTime(0.08, now + 0.015);
    gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.32);

    first.type = 'sine';
    second.type = 'sine';
    first.frequency.setValueAtTime(kind === 'success' ? 740 : 220, now);
    second.frequency.setValueAtTime(kind === 'success' ? 980 : 165, now + 0.1);

    first.connect(gain);
    second.connect(gain);
    first.start(now);
    first.stop(now + 0.16);
    second.start(now + 0.12);
    second.stop(now + 0.32);
}

export function playSuccessSound(): void {
    playTone('success');
}

export function playErrorSound(): void {
    playTone('error');
}
