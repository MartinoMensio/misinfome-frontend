import { Component, OnInit } from '@angular/core';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { AccuracyModalComponent } from '../accuracy-modal/accuracy-modal.component';
import { AnalysisModalComponent } from '../analysis-modal/analysis-modal.component';

@Component({
  selector: 'app-about',
  templateUrl: './about.component.html',
  styleUrls: ['./about.component.css']
})
export class AboutComponent implements OnInit {

  constructor(private modalService: NgbModal) {}

  ngOnInit(): void {
  }


  open_analysis_modal() {
    const modalRef = this.modalService.open(AnalysisModalComponent);
  }
  
  open_accuracy_modal() {
    const modalRef = this.modalService.open(AccuracyModalComponent);

  }

}
