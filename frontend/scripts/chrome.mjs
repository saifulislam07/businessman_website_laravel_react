import { existsSync } from 'node:fs'

const CANDIDATES = [
  'C:/Program Files/Google/Chrome/Application/chrome.exe',
  'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe',
  'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe',
  '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
  '/usr/bin/google-chrome',
  '/usr/bin/google-chrome-stable',
  '/usr/bin/chromium',
  '/usr/bin/chromium-browser',
]

/** Path to a local Chrome/Chromium; set CHROME_PATH to override. */
export function findChrome() {
  const path = process.env.CHROME_PATH ?? CANDIDATES.find((p) => existsSync(p))
  if (!path) {
    throw new Error('Chrome/Chromium not found. Install it or set the CHROME_PATH environment variable.')
  }
  return path
}
