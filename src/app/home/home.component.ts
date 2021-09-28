import { Component, OnInit } from '@angular/core';
import { ApiService, LoadStates } from '../api.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
  
  loadStates = LoadStates;
  load_state = LoadStates.None;
  data: any;
  error_detail = '';
  countUpOptions={duration:5, decimalPlaces:2};

  constructor(private apiService: ApiService) { }

  ngOnInit(): void {
    this.load_state = LoadStates.Loading;
    this.apiService.getHome().subscribe(homeData => {
      this.data = homeData;
      this.load_state = LoadStates.Loaded;
    }, error => {
      this.load_state = LoadStates.Error;
      this.error_detail = error.error.detail;
    })
  }

}
