import {coupleScroll, buildCommentElement} from '../../amd/src/logic.js';

/**
 * Minimal fake scroll element: records handlers and lets a test fire them.
 *
 * @returns {object} The fake element.
 */
const makeEl = () => {
    const handlers = {};
    return {
        scrollTop: 0,
        scrollLeft: 0,
        addEventListener: (type, fn) => {
            (handlers[type] = handlers[type] || []).push(fn);
        },
        fire: (type) => (handlers[type] || []).forEach((fn) => fn()),
    };
};

describe('coupleScroll', () => {
    test('mirrors scroll position from one pane to the other', () => {
        const a = makeEl();
        const b = makeEl();
        coupleScroll(a, b);

        a.scrollTop = 120;
        a.scrollLeft = 30;
        a.fire('scroll');

        expect(b.scrollTop).toBe(120);
        expect(b.scrollLeft).toBe(30);
    });

    test('the re-entrancy guard prevents a mirrored write from echoing back', () => {
        const a = makeEl();
        const b = makeEl();
        coupleScroll(a, b);

        // A scrolls -> b is mirrored and syncing is armed.
        a.scrollTop = 200;
        a.fire('scroll');
        expect(b.scrollTop).toBe(200);

        // The mirrored assignment would fire b's scroll; that echo must NOT
        // write back onto a.
        a.scrollTop = 999; // Would be overwritten if the echo wrote back.
        b.fire('scroll');
        expect(a.scrollTop).toBe(999);
    });

    test('a genuine second scroll still mirrors after the guard resets', () => {
        const a = makeEl();
        const b = makeEl();
        coupleScroll(a, b);

        a.scrollTop = 10;
        a.fire('scroll');
        b.fire('scroll'); // Echo, consumes the guard.

        a.scrollTop = 55;
        a.fire('scroll'); // Genuine.
        expect(b.scrollTop).toBe(55);
    });
});

describe('buildCommentElement', () => {
    test('renders author and body as text', () => {
        const li = buildCommentElement(document, {authorname: 'Alice', content: 'Nice map'});
        expect(li.querySelector('.vimigallery-comment-meta').textContent).toBe('Alice');
        expect(li.querySelector('.vimigallery-comment-body').textContent).toBe('Nice map');
    });

    test('does not inject markup from the comment body', () => {
        const li = buildCommentElement(document, {authorname: '', content: '<b>x</b>'});
        const body = li.querySelector('.vimigallery-comment-body');
        expect(body.textContent).toBe('<b>x</b>');
        expect(body.querySelector('b')).toBeNull();
    });
});
