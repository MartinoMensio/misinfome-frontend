import { Component, OnInit } from '@angular/core';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-accuracy-modal',
  templateUrl: './accuracy-modal.component.html',
  styleUrls: ['./accuracy-modal.component.css']
})
export class AccuracyModalComponent implements OnInit {

  constructor(public activeModal: NgbActiveModal) {}

  ngOnInit(): void {
  }

}
