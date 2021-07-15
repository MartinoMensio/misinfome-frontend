import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AnalysisModalComponent } from './analysis-modal.component';

describe('AnalysisModalComponent', () => {
  let component: AnalysisModalComponent;
  let fixture: ComponentFixture<AnalysisModalComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ AnalysisModalComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(AnalysisModalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
