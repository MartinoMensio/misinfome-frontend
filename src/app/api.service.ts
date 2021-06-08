import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  API_URL = environment.api_url;

  constructor(private httpClient: HttpClient) { }

  getHome() {
    return this.httpClient.get(`${this.API_URL}/frontend/v2/home/`)
  }
}
