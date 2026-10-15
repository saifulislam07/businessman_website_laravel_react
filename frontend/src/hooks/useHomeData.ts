import { useQuery } from '@tanstack/react-query'
import { getHome } from '../api'
import { readPrerenderedData } from '../lib/prerendered'

// Read once: the data embedded in the prerendered HTML at build time (undefined in dev).
const prerendered = readPrerenderedData()

export function useHomeData() {
  return useQuery({
    queryKey: ['home'],
    queryFn: getHome,
    staleTime: 5 * 60 * 1000,
    // Prerendered data paints instantly; epoch 0 marks it stale so fresh data is fetched right away.
    initialData: prerendered,
    initialDataUpdatedAt: 0,
  })
}
