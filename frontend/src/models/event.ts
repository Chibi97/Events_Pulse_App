export type EventType = 'Basic' | 'Vip' | 'Exclusive'

export interface Event {
  readonly id: string
  readonly name: string
  readonly current_price: string
  readonly date: string
  readonly image_url: string | null
  readonly base_price: string
  readonly is_for_sale: boolean
  readonly event_type: EventType
}
