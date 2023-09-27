export interface Quote {
  symbol: string
  company_name: string
  type: string
  exchange: string
  exchange_name: string
  price: number
  currency: string
  open: number
  close: number
  low: number
  high: number
  previous_close: number
  change: number
  change_percent: number
  volume: number
  market_cap: number
  '52_week_low': number
  '52_week_low_change': number
  '52_week_low_change_percent': number
  '52_week_high': number
  '52_week_high_change': number
  '52_week_high_change_percent': number
  shares_outstanding: number
  eps_ttm: number
  dividend_yield_ta: number
  dividend_rate_ta: number
  last_update: number
}

interface QuoteObject {
  quote: Quote
}

type InputJson = Record<string, QuoteObject>

export function getQuoteArrayFromYahooData(input: InputJson): Quote[] {
  const result: Quote[] = []

  for (const symbol in input) {
    if (Object.prototype.hasOwnProperty.call(input, symbol)) {
      result.push(input[symbol].quote)
    }
  }

  return result
}
export function keysToString(obj: Record<string, any>): string {
  const keys = Object.keys(obj)
  return keys.join(',')
}
