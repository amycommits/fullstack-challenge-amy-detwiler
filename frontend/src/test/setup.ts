import { beforeEach, afterEach, vi } from 'vitest'

beforeEach(() => {
  // Create app element for each test
  const app = document.createElement('div')
  app.id = 'app'
  document.body.appendChild(app)

  // Mock matchMedia
  window.matchMedia = vi.fn().mockImplementation(query => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: vi.fn(),
    removeListener: vi.fn(),
    addEventListener: vi.fn(),
    removeEventListener: vi.fn(),
    dispatchEvent: vi.fn(),
  }))
})

afterEach(() => {
  // Clean up app element
  const app = document.getElementById('app')
  if (app) {
    app.remove()
  }
}) 
