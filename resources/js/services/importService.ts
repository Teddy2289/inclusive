import api from './api'

export const importService = {
  async importPartenaires(file: File): Promise<{ message: string }> {
    const formData = new FormData()
    formData.append('file', file)

    const { data } = await api.post('/import', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return data
  }
}
