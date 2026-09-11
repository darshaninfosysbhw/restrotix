const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');

test('customer countdown waits for chef, ticks, resyncs, and stops at ready', () => {
    const blade = fs.readFileSync(path.join(__dirname, '../../resources/views/modules/public-menu/order-status.blade.php'), 'utf8');
    const source = blade.slice(blade.indexOf('        let preparationTimer ='), blade.indexOf('        function applyOrderStatusSnapshot'))
        .replace('@json($preparationTimer)', 'null');
    const nodes = Object.fromEntries(['preparationTimerCard', 'preparationTimerLabel', 'preparationTimerValue', 'preparationTimerNote'].map(id => [id, {}]));
    let elapsed = 0;
    const context = vm.createContext({ document: { getElementById: id => nodes[id] }, performance: { now: () => elapsed } });
    vm.runInContext(source, context);
    const render = timer => {
        context.payload = timer;
        vm.runInContext('preparationTimer = payload; preparationTimerReceivedAt = performance.now(); renderPreparationTimer();', context);
    };
    render({ state: 'waiting', estimated_minutes: 20 });
    assert.equal(nodes.preparationTimerValue.textContent, '20 min');
    elapsed = 60000;
    vm.runInContext('renderPreparationTimer()', context);
    assert.equal(nodes.preparationTimerValue.textContent, '20 min');
    const preparing = { state: 'preparing', server_now: '2026-09-11T12:00:00Z', ready_at: '2026-09-11T12:20:00Z' };
    render(preparing);
    assert.equal(nodes.preparationTimerValue.textContent, '20:00');
    elapsed += 1000;
    vm.runInContext('renderPreparationTimer()', context);
    assert.equal(nodes.preparationTimerValue.textContent, '19:59');
    render({ ...preparing, server_now: '2026-09-11T12:05:00Z', has_waiting_items: true, waiting_minutes: 30 });
    assert.equal(nodes.preparationTimerValue.textContent, '15:00');
    assert.match(nodes.preparationTimerNote.textContent, /30 min/);
    elapsed += 900000;
    vm.runInContext('renderPreparationTimer()', context);
    assert.equal(nodes.preparationTimerValue.textContent, 'Taking a little longer');
    render({ state: 'ready' });
    assert.equal(nodes.preparationTimerValue.textContent, 'Your order is ready');
    render({ state: 'preparing', has_unknown_time: true });
    assert.equal(nodes.preparationTimerValue.textContent, 'Preparation in progress');
    render({ state: 'unavailable' });
    assert.equal(nodes.preparationTimerCard.hidden, true);
});
