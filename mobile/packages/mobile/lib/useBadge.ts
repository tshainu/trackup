/**
 * useBadge — tracks new/pending jobs per tab.
 *
 * States:
 *  - Red dot  = unseen new jobs (never opened this tab since jobs appeared)
 *  - Green dot = opened tab, saw jobs, but jobs still active (not all cleared)
 *  - No dot   = all jobs cleared (empty list or all completed)
 */
import { useState, useEffect, useCallback } from 'react';

type TabKey = 'jobs' | 'field';

export type BadgeState = 'red' | 'green' | 'none';

// Module-level store — persists across re-renders, resets on app restart
const seenStore: Record<string, Set<number>> = {};

function getSeenSet(tab: TabKey): Set<number> {
  if (!seenStore[tab]) seenStore[tab] = new Set();
  return seenStore[tab];
}

export function useBadge(tab: TabKey, currentIds: number[]) {
  const [badge, setBadge] = useState<BadgeState>('none');
  const idsKey = currentIds.join(',');

  useEffect(() => {
    if (currentIds.length === 0) {
      setBadge('none');
      return;
    }
    const seen = getSeenSet(tab);
    const anyUnseen = currentIds.some(id => !seen.has(id));
    if (anyUnseen) {
      setBadge('red');   // new jobs user hasn't seen
    } else {
      setBadge('green'); // seen but jobs still active
    }
  }, [idsKey]);

  const markAllSeen = useCallback(() => {
    const seen = getSeenSet(tab);
    currentIds.forEach(id => seen.add(id));
    // If there are still active jobs → green, else none
    setBadge(currentIds.length > 0 ? 'green' : 'none');
  }, [idsKey]);

  // Convenience booleans
  const hasNew = badge === 'red';
  const hasSeen = badge === 'green';

  return { badge, hasNew, hasSeen, markAllSeen };
}
