import type { ContactPayload, ContactResponse, HomeData } from '../types'
import { api } from './client'

export async function getHome(): Promise<HomeData> {
  const { data } = await api.get<{ data: HomeData }>('/home')
  return data.data
}

export async function postContact(payload: ContactPayload): Promise<ContactResponse> {
  const { data } = await api.post<ContactResponse>('/contact', payload)
  return data
}
