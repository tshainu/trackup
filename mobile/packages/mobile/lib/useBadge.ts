/**
 * useBadge — tracks "seen" job IDs per tab (in-memory, no native deps).
 * Red dot = there are new (unseen) jobs since app opened.
 */
import { useState, useEffect, useCallback, useRef } from 'react';

type TabKey = 'jobs' | 'field';

// Module-level store so seen IDs persist across re-renders (but reset on app restart)
const seenStore: Record<string, Set<number>> = {};

function getSeenSet(tab: TabKey): Set<number> {
  if (!seenStore[tab]) seenStore[tab] = new Set();
  return seenStore[tab];
}

export function useBadge(tab: TabKey, currentIds: number[]) {
  const [hasNew, setHasNew] = useState(false);
  const idsKey = currentIds.join(',');

  useEffect(() => {
    if (currentIds.length === 0) {
      setHasNew(false);
      return;
    }
    const seen = getSeenSet(tab);
    const anyNew = currentIds.some(id => !seen.has(id));
    setHasNew(anyNew);
  }, [idsKey]);

  const markAllSeen = useCallback(() => {
    const seen = getSeenSet(tab);
    currentIds.forEach(id => seen.add(id));
    setHasNew(false);
  }, [idsKey]);

  return { hasNew, markAllSeen };
}
