export function formatPrice(price: string) {
  const priceWithTwoDecimals = parseFloat(price).toFixed(2)
  return `$${priceWithTwoDecimals}`
}
