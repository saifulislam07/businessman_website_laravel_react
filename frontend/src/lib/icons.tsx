import {
  Award,
  Briefcase,
  Building,
  Building2,
  Calendar,
  Code,
  Cpu,
  Factory,
  Globe,
  Handshake,
  Heart,
  Home,
  Landmark,
  Layers,
  Leaf,
  Lightbulb,
  Plane,
  Rocket,
  ShieldCheck,
  Ship,
  Store,
  Target,
  TrendingUp,
  Truck,
  Users,
  type LucideIcon,
  type LucideProps,
} from 'lucide-react'

/** Icons that can be chosen by name in the admin panel. Names match case-insensitively. */
const ICONS: Record<string, LucideIcon> = {
  award: Award,
  briefcase: Briefcase,
  building: Building,
  building2: Building2,
  calendar: Calendar,
  code: Code,
  cpu: Cpu,
  factory: Factory,
  globe: Globe,
  handshake: Handshake,
  heart: Heart,
  home: Home,
  landmark: Landmark,
  layers: Layers,
  leaf: Leaf,
  lightbulb: Lightbulb,
  plane: Plane,
  rocket: Rocket,
  shieldcheck: ShieldCheck,
  ship: Ship,
  store: Store,
  target: Target,
  trendingup: TrendingUp,
  truck: Truck,
  users: Users,
}

interface DynamicIconProps extends LucideProps {
  icon: string | null | undefined
}

/** Renders the icon chosen in the admin, falling back to Briefcase. */
export function DynamicIcon({ icon, ...props }: DynamicIconProps) {
  const key = (icon ?? '').toLowerCase().replace(/[^a-z0-9]/g, '')
  const Icon = ICONS[key] ?? Briefcase
  return <Icon {...props} />
}
