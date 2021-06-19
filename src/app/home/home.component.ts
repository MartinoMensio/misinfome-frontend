import { Component, OnInit } from '@angular/core';
import { ApiService } from '../api.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
  
  loading = true;
  data: any;
  countUpOptions={duration:5};

  constructor(private apiService: ApiService) { }

  ngOnInit(): void {
    this.apiService.getHome().subscribe(homeData => {
      this.data = homeData;
      this.loading = false;
    })
  }

}
