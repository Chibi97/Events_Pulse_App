import defaultTheme from 'tailwindcss/defaultTheme'

/** @type {import('tailwindcss').Config} */
export const content = ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}']
export const theme = {
  screens: {
    xs: '200px',
    'sm-md--middle': '712px',
    ...defaultTheme.screens,
  },
  extend: {
    screens: {
      '3xl': '1624px',
      '4xl': '2000px',
    },
    minHeight: {
      'screen-without-footer': 'calc(100vh - 292px)',
    },
    fontFamily: {},
  },
}
export const plugins = [
  function ({ addBase, theme }) {
    function extractColorVars(colorObj, colorGroup = '') {
      return Object.keys(colorObj).reduce((vars, colorKey) => {
        const value = colorObj[colorKey]

        const newVars =
          typeof value === 'string'
            ? { [`--color${colorGroup}-${colorKey}`]: value }
            : extractColorVars(value, `-${colorKey}`)

        return { ...vars, ...newVars }
      }, {})
    }

    addBase({
      ':root': extractColorVars(theme('colors')),
    })
  },
]
